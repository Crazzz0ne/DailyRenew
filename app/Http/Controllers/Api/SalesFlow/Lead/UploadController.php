<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\SendFileToEPCEvent;
use App\Events\Backend\SalesFlow\LeadEvent;
use App\Events\Backend\SalesFlow\LeadFileUploadEvent;
use App\Events\Backend\SalesFlow\SalesPacketEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Lead\StoreFileRequest;
use App\Http\Resources\LeadUploadResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUpload;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use League\Flysystem\Exception;
use League\Flysystem\Filesystem;

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Spatie\Newsletter\NewsletterFacade as Newsletter;
use Throwable;
use ZipArchive;


class UploadController extends Controller
{

    protected $leadRepository;

    public function __construct(Lead $lead)
    {
        // set the model
        $this->leadRepository = new LeadRepository();
    }

    public function getSubscriberHash($email)
    {
        $mailChimp = Newsletter::getApi();

        return $mailChimp->subscriberHash($email);
    }

    public function findListByName(string $listName)
    {
        $mailChimp = Newsletter::getApi();

        $lists = $mailChimp->get('lists');

        foreach ($lists['lists'] as $list) {
            if ($list['name'] === $listName) {
                $id = $list['id'];
            }
        }

        return $id;
    }

    public function createTags(string $email, string $listName = '', array $tags = [])
    {


        $mailChimp = Newsletter::getApi();

        $list = $this->findListByName($listName);

        return $mailChimp->get("/lists/{$list}/members/{$this->getSubscriberHash($email)}/tags", $tags);

        return $response;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index($id)
    {


    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {

        // getting all of the post data
        $files = $request->file('files');


//        // Making counting of uploaded images
//        $file_count = count($files);

        // start count how many uploaded
        $uploadCount = 0;
        $uploadArray = [];

        foreach ($files as $file) {


//            / there can only be one!
            if ($request->type === 'signed solar agreement' || $request->type === 'signed nem'
                || $request->type === 'ach form' || $request->type === 'signed CPUC' ||
                $request->type === 'signed ach') {
                $pastUpload = LeadUpload::where('lead_id', '=', $request->lead_id)
                    ->where('type', '=', $request->type)->first();
                if ($pastUpload != null) {
                    $pastUpload->delete();
                }

            }
//            fail('608', 'We did not get the file trying again');
            try {

                $uploadFile = UploadedFile::createFromBase($file);

                UploadedFile::createFromBase($file)->get();
            } catch (Throwable $e) {
                $collection = [
                    'status' => 69,
                    'error' => $e,
                ];
                $payload = collect($collection);
                return $payload;
            }
///

///

            // Generate unique name with real extension using the same method used by Storage::putFile()
            $fileHash = str_replace('.' . $uploadFile->extension(), '', $uploadFile->hashName());
            $fileName = $fileHash . '.' . $file->getClientOriginalExtension();

            $path = Storage::disk('s3')->putFileAs('lead/' . $request->lead_id . '/' . $request->type,
                $uploadFile, $fileName, 'private');
            $filesize = filesize($file); // bytes
            $size = round($filesize / 1024 / 1024, 1);

            $upload = new LeadUpload();
            $upload->lead_id = $request->lead_id;
            $upload->user_id = $request->user_id;
            $upload->type = $request->type;
            $upload->size = $size;
            $upload->path = $path;
            $upload->save();

            $lead = Lead::find($upload->lead_id);
            $sp = SalesPacket::find($lead->sales_packet_id);
            $now = Carbon::now()->toDateTimeString();
            $sendComplete = false;
            $completeFileType = '';
            $spI = 0;
            switch ($upload->type) {
                case 'bill':
                    $sendComplete = true;
//  Fire UpdateZapierEvent
                    event(new UpdateZapierEvent($lead, 'bill'));

                    break;
                case 'survey pictures':
                    $sendComplete = true;
                    break;
                case 'signed CPUC':
                    $sp->cpuc_doc_signed = $now;
                    $sendComplete = true;
                    $spI++;
                    break;
                case 'signed NEM':
                    $sp->nem_doc_signed = $now;
                    $sendComplete = true;
                    $spI++;
                    break;
                case 'signed solar agreement':
                    $sp->solar_agreement_signed = $now;
                    $sendComplete = true;
                    $spI++;
                    break;
                case 'quote':
                    $sp->quote = $now;
                    $spI++;
                    break;
                case 'signed ACH':
                    $sp->ach_doc_signed = $now;
                    $sendComplete = true;
                    $spI++;
                    break;
                case 'signed credit check':
                    $sp->credit_doc_signed = $now;
                    $spI++;
                    break;
                case 'site plan':
                    $sp->site_plan = $now;
                    $spI++;
                    break;
                case 'CCA':
                    $sp->cpuc_doc_signed = $now;
                    $sp->ach_doc_signed = $now;
                    $sp->credit_doc_signed = $now;
                    $spI++;
                    break;
                case 'CA':
                    $sp->credit_doc_signed = $now;
                    $sp->ach_doc_signed = $now;
                    $spI++;
                    break;
                default:
                    break;
            }
//            if ($spI) {
//                $sp->save();
//
//                if ($lead->salesPacket->solar_agreement_signed && $lead->salesPacket->cpuc_doc_signed
//                    && $lead->salesPacket->credit_doc_signed && $lead->salesPacket->ach_doc_signed
//                    && $lead->salesPacket->nem_doc_signed) {
//
//                    $lead->status = 'close';
//                    $lead->save();
//
//                    $subject = 'All docs signed (' . $lead->id . ')';
//                    $body = 'Congratulations all of your documents have been signed and have been submitted for approval
//                             for ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' ';
//
//
////                    We were sending to the PB when a bill was uploaded but just going to send to ALL of the PBS
////                    $rep = UserHasLead::where('lead_id', '=', $lead->id)
////                        ->where('position_id', '=', 5)
////                        ->get()
////                        ->first();
//
////                    $this->email($subject, $body, $link, $rep);
//
//
//                }
//
//
//                $something = $sp->getChanges();
////        gets only the changes
//
//                if (count($something) > 0) {
////        I need the ID for vue to match on the page
//                    $something['id'] = $sp->id;
//                    $data = collect($something);
////        lets everyone else know of the changes
//                    event(new \App\Events\Backend\SalesFlow\Lead\SalesPacketEvent($data, $lead->id));
//                }
//                event(new LeadEvent($lead));
//            }

            if ($sendComplete) {
//               event(new SendFileToEPCEvent($upload, $lead));
            }

            if ($lead->status_id == 5 && $request->type === 'bill') {
                $link = URL::to('/') . "/dashboard/lead/" . $lead->id;
                Mail::to('proposals@solcalenergy.com')
                    ->queue(new BaseMailable('new upload(' . $upload->id . ')', 'new bill uploaded lead #' . $lead->id, $link, 'lead',));
            }
            $upload = LeadUpload::where('id', $upload->id)->with('user')->first();
            $uploadPush = new LeadUploadResource($upload);
            array_push($uploadArray, $uploadPush);
            $something = new LeadUploadResource($upload);
            event(new LeadFileUploadEvent($request->lead_id, $something));
        }
        $collection = [
            'status' => 200,
            'files' => $uploadArray
        ];
        $payload = collect($collection);
        return $payload;
    }

    public function downloadAll(Lead $lead)
    {
        $customerName = $lead->customer->last_name;

        // see laravel's config/filesystem.php for the source disk
        $source_disk = 's3';
        $source_path = '';

        $files = LeadUpload::where('lead_id', $lead->id)->get();
        $zipLocation = $lead->id . '_' . $customerName . '.zip';
        $zip = new Filesystem(new ZipArchiveAdapter(public_path($zipLocation)));
        $storage = Storage::disk($source_disk);


        $bill = 0;
        $surveyPictures = 0;
        foreach ($files as $file) {

            $file_content = $storage->get($file->path);
            $type = $storage->mimeType($file->path);
            $type = substr($type, strpos($type, "/") + 1);
            $fileName = $file->type . '.' . $type;


            switch ($file->type) {
                case 'bill';
                    $bill++;
                    $zip->put($customerName . '(' . $bill . ')' . '-' . $fileName, $file_content);
                    break;
                case 'survey pictures';
                    $surveyPictures++;
                    $zip->put($customerName . '(' . $surveyPictures . ')' . '-' . $fileName, $file_content);
                    break;
                default;
                    $zip->put($customerName . '-' . $fileName, $file_content);
                    break;
            }

        }

        $zip->getAdapter()->getArchive()->close();


        return redirect($zipLocation);

    }

    /**
     * Display the specified resource.
     *
     * @param Lead $lead
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Lead $lead)
    {

        return LeadUploadResource::collection(LeadUpload::where('lead_id', $lead->id)->orderBy('id', 'desc')->with('user')->get());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LeadUpload $upload
     * @return string
     * @throws \Exception
     */
    public function destroy(Lead $lead, LeadUpload $upload): string
    {


        $upload->delete();
        return 'yes';
    }
}
