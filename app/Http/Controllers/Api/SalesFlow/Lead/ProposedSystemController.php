<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\Closed\CompleteClosedEvent;
use App\Events\Backend\SalesFlow\Lead\CustomerSatEvent;
use App\Events\Backend\SalesFlow\Lead\LeadClosedEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\ProposedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\SalesPacketEvent;
use App\Events\Backend\SalesFlow\LeadFileUploadEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\Lead\ProposedSystemPPWResource;
use App\Http\Resources\Lead\ProposedSystemResource;
use App\Http\Resources\LeadUploadResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\Epc\SolarModule;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadStatus;
use App\Models\SalesFlow\Lead\LeadUpload;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\System;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use App\Repositories\BaseRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Slack;

class ProposedSystemController extends Controller
{

    protected $leadRepository;

    public function __construct(Lead $lead)
    {
        // set the model
        $this->leadRepository = new LeadRepository();
    }

    public function index(Lead $lead)
    {
        $user = \Auth::user();
        //TODO: Why testDoc?
        $proposedSystem = ProposedSystem::where('lead_id', '=', $lead->id)->with('proposalDoc', 'testDoc')->get();

        $line = Line::where('type', 'build_proposal')->where('lead_id', $lead->id)->with('requestingUser', 'filledUser')->get();

        if ($user->hasPermissionTo('view ppw')) {
            $proposedSystem = ProposedSystemPPWResource::collection($proposedSystem);
        } else {
            $proposedSystem = ProposedSystemResource::collection($proposedSystem);
        }


        $line = LineResource::collection($line);
        $data = ['line' => $line, 'proposedSystem' => $proposedSystem];

        return collect($data);
    }

    public function create(Lead $lead, Request $request)
    {
        $user = \Auth::user();

        $proposedSystem = new ProposedSystem();
        $proposedSystem->lead_id = $lead->id;
        $proposedSystem->save();

        if ($user->hasPermissionTo('view ppw')) {
            $sendIt = new ProposedSystemPPWResource($proposedSystem);
        } else {
            $sendIt = new ProposedSystemResource($proposedSystem);
        }

        event(new ProposedSystemEvent($sendIt, $lead->id, false));

        return $sendIt;

    }

    public function show()
    {

    }

    public function update(Lead $lead, ProposedSystem $proposedSystem, Request $request)
    {

        $proposedSystem->update($request->except('contract_amount'));

        if ($request->contract_amount) {
            $proposedSystem->contract_amount = str_replace(array(','), '', $request->contract_amount);
            $proposedSystem->save();
        }


//        gets only the changes
        $something = $proposedSystem->getChanges();
        if (count($something) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $proposedSystem->id;

            if (($proposedSystem->modules_count && $proposedSystem->modules_id) && $proposedSystem->system_size === null) {
                $watts = SolarModule::where('id', $proposedSystem->modules_id)->pluck('watts')
                    ->first();
                $systemSize = ($watts * $proposedSystem->modules_count) / 1000;
                $proposedSystem->system_size = $systemSize;
                $proposedSystem->save();
                $something['system_size'] = $systemSize;

            }
            $data = collect($something);
//        lets everyone else know of the changes
            event(new ProposedSystemEvent($data, $lead->id));
        }
//       I dont care about the reply outside of it being a 200
        return $something;
    }

    public function upload(Lead $lead, Request $request)
    {
        $user = \Auth::user();
        $file = $request->file;

        $path = Storage::disk('s3')->put('lead/' . $lead->id . '/' . $request->type, $file, 'private');
        $filesize = filesize($file); // bytes
        $size = round($filesize / 1024 / 1024, 1);

        $psCount = ProposedSystem::where('lead_id', '=', $lead->id)->get()->count();

        if ($request->type === 'proposal') {
            $fileName = 'Design Plan (' . $psCount . ')';
        } else {
            $fileName = 'Savings Break Down (' . $psCount . ')';
        }


        $upload = new LeadUpload();

        $upload->lead_id = $lead->id;
        $upload->user_id = $user->id;
        $upload->type = $request->type;
        $upload->size = $size;
        $upload->path = $path;
        $upload->name = $fileName;

        $upload->save();


        $ps = ProposedSystem::where('id', $request->proposedsystemId)
            ->first();

        if ($request->type === 'Design Plan') {
            $ps->site_plan_doc_id = $upload->id;
        } else {
            $ps->proposal_doc_id = $upload->id;
        }

        $ps->save();

        $payload = new LeadUploadResource($upload);
        event(new LeadFileUploadEvent($lead->id, $payload));


        if ($user->hasPermissionTo('view ppw')) {
            $sendIt = new ProposedSystemPPWResource($ps);
        } else {
            $sendIt = new ProposedSystemResource($ps);
        }
        event(new ProposedSystemEvent($sendIt, $lead->id, false));
        return $payload;
    }


    public function approve(Lead $lead, ProposedSystem $proposedSystem, Request $request)
    {
        return $request;

    }

    public function submitToRep(Lead $lead, $id, Request $request)
    {

        $apiKey = substr($request->header('Authorization'), -80);
        $user = Auth::user();

        $proposedSystem = ProposedSystem::where('id', $id)->first();
        $proposedSystem->pb_design_approved = $user->id;
        $proposedSystem->save();

        $link = URL::to('/') . '/dashboard/lead/' . $lead->id;

        $rep = UserHasLead::where(function ($q) {
            return $q
                ->where('position_id', 3)
                ->orWhere('position_id', 5);
        })
            ->where('lead_id', '=', $lead->id)
            ->with('user')
            ->first();

        if ($rep) {
            $email = $rep->user;
            if ($email) {
                HelperController::email('System ready to review', 'We have completed a proposed system please review', $link, $email, 'System');

            }
        }

        $something = $proposedSystem->getChanges();
        if (count($something) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $proposedSystem->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new ProposedSystemEvent($data, $lead->id, false));
        }

        return 'submitted';

    }

    public function repApproved(Lead $lead, $id, Request $request)
    {

        $user = \Auth::user();

        try {
            $proposedSystem = ProposedSystem::where('id', $id)->first();
            $proposedSystem->rep_design_approved = $user->id;
            $proposedSystem->save();
        } catch (\Exception $exception) {
            Log::error('Proposed System Failed', [request()->fullUrl()]);
            $proposedSystem = ProposedSystem::where('id', $lead->proposedSystems[0]->id)->first();
            $proposedSystem->rep_design_approved = $user->id;
            $proposedSystem->save();
        }

        $lead->close_date = Carbon::now()->timezone('America/los_angeles')->toDateString();
        $lead->save();

        $something = $proposedSystem->getChanges();
        if (count($something) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $proposedSystem->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new ProposedSystemEvent($data, $lead->id));
        }


        if (($proposedSystem->modules_count && $proposedSystem->modules_id) && $proposedSystem->system_size === null) {
            $watts = SolarModule::where('id', $proposedSystem->modules_id)->pluck('watts')
                ->first();
            $systemSize = ($watts * $proposedSystem->modules_count) / 1000;
            $proposedSystem->system_size = $systemSize;
            $proposedSystem->save();
        }

        $system = new System();
        $system->inverter_id = $proposedSystem->inverter_id;
        $system->modules_count = $proposedSystem->modules_count;
        $system->modules_id = $proposedSystem->modules_id;
        $system->epc_finance_id = $proposedSystem->epc_finance_id;
        $system->system_size = $proposedSystem->system_size;
        $system->monthly_payment = $proposedSystem->monthly_payment ?? 0;
        $system->contract_amount = $proposedSystem->contract_amount;
        $system->yearly_production = $proposedSystem->yearly_production;
        $system->solar_rate = $proposedSystem->solar_rate;
        $system->offset = $proposedSystem->offset;
        $system->adders = $proposedSystem->adders;
        $system->roof_work = $proposedSystem->roof_work;
        $system->external_proposal_id = $proposedSystem->external_proposal_id;
        $system->ppw = $proposedSystem->ppw;
        $system->save();


        ProposedSystem::where('lead_id', '=', $lead->id)
            ->delete();

        RequestedSystem::where('lead_id', '=', $lead->id)
            ->delete();

//        SalesPacket::where('id', $lead->sales_packet_id)->update(['sat' => true]);

        if ($lead->epc_id === 1) {

            $lead->status_id = 7;
//            $lead->proposed_system_id = $proposedSystem->id;

        } else {
            $lead->status_id = 6;
//            $lead->proposed_system_id = $proposedSystem->id;


            $line = new Line();
            $line->requested_user_id = $user->id;
            $line->lead_id = $lead->id;
            $line->type = 'send_paperwork';
            $line->save();

            $subject = 'Send Paperwork to Customer (' . $line->lead_id . ')';
            $body = 'Can you log into docusign and send paperwork to the customer?';
            $link = URL::to('/') . '/dashboard/lead/queue/';
            $ntsPeoples = User::permission('edit NTS')->get();
            $ntsCount = 0;
            foreach ($ntsPeoples as $nts) {

                Mail::to($nts->email)->queue(new BaseMailable($subject, $body, $link, 'Paper Work'));

                $ntsCount++;
            }

        }

        $lead->system_id = $system->id;
        $lead->save();
        if ($proposedSystem->epc === 'complete') {
            event(new CompleteClosedEvent($lead->id, $lead));

        }
        event(new UpdateZapierEvent($lead, 'sit'));

        if ($lead->epc_id === 1) {
            $salesPacket = SalesPacket::where('id', $lead->sales_packet_id)->first();
//            if ($lead->origin_office_id === 10 && !$salesPacket->sat) {
//                $this->leadRepository->callCenterCommission($lead, $user, 3);
//            }

            $now = Carbon::now()->toDateTimeString();

            $salesPacket->solar_agreement_signed = $now;
            $salesPacket->cpuc_doc_signed = $now;
            $salesPacket->sat = true;
            $salesPacket->save();
            event(new CustomerSatEvent($lead, $user));

            $salesPacketData = $salesPacket->getChanges();

            if (count($salesPacketData) > 0) {
//        I need the ID for vue to match on the page
                $something['id'] = $salesPacket->id;
                $data = collect($salesPacketData);
//        lets everyone else know of the changes
                event(new SalesPacketEvent($data, $lead->id));
            }


            $line = new Line();
            $line->requested_user_id = $user->id;
            $line->lead_id = $lead->id;
            $line->type = 'NTS';
            $line->save();


            $subject = 'NTS (' . $line->lead_id . ')';
            $body = 'Can You Submit Paperwork?';
            $link = URL::to('/') . '/dashboard/lead/queue';
            $ntsPeoples = User::permission('edit NTS')->where('terminated', null)->get();
//            if env is production, send to all NTS
            if (env('APP_ENV') === 'production') {
                foreach ($ntsPeoples as $nts) {
                    if ($lead->source === 'call center' || $lead->source === 'call center alpha') {
                        Mail::to($nts->email)->queue(new BaseMailable($subject, $body, $link, 'NTS'));
                    } elseif ($nts->id === 12) {
                        Mail::to($nts->email)->queue(new BaseMailable($subject, $body, $link, 'NTS'));
                    }
                }
            }


            $options = [
                'color' => 'good',
                'fields' => [
                    [
                        'title' => 'Paperwork Request!',
                        'value' => "{$line->requestingUser->name} is requesting NTS for {$line->lead->customer->name}!",
                        'short' => false,
                    ]
                ],
            ];

//            Slack::compose(Slack::link(config('app.url') . '/dashboard/lead/queue', 'View Queue'), config('slack.channels.project_management'), $options);
        }
        $leadChanges = $lead->getChanges();
        if (count($leadChanges) > 0) {
//        I need the ID for vue to match on the page
            if (isset($leadChanges['status_id'])) {
                $statusName = LeadStatus::where('id', $leadChanges['status_id'])->first();
                $leadChanges['status'] = $statusName->name;
            }
//        lets everyone else know of the changes
            event(new LeadUpdateEvent($leadChanges, $lead->id));
        }

        $size = $lead->system->system_size;
        $ppw = $lead->system->ppw;
        $body = $lead->customer->first_name . ' ' . $lead->customer->last_name . ' CLOSED by ' . $user->first_name . ' ' . $user->last_name . ' 🤑💸
            Size: ' . $size . '
            PPW: $' . $ppw;
//        if ($lead->origin_office_id === 5) {
//            event(new TextEvent('6199406423', $body));
//        } else {
            event(new TextEvent('8564306685', $body));
            event(new TextEvent('6199406423', $body));
            event(new TextEvent('9106907839', $body));
            event(new TextEvent('7654093132', $body));
            event(new TextEvent('9109867050', $body));
//        }


//        send ZapierEvent
        return $system;


    }

    public function repApprovedChangeOrder(Lead $lead, $id, Request $request)
    {

        $user = Auth::user();

        $proposedSystem = ProposedSystem::where('id', $id)->first();
        $proposedSystem->rep_design_approved = $user->id;
        $proposedSystem->save();


        $something = $proposedSystem->getChanges();
        if (count($something) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $proposedSystem->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new ProposedSystemEvent($data, $lead->id));
        }

        $system = System::where('id', $lead->system_id)->first();
        $system->epc_system_id = $proposedSystem->epc_system_id;
        $system->epc_finance_id = $proposedSystem->epc_finance_id;
        $system->system_size = $proposedSystem->system_size;
        $system->monthly_payment = $proposedSystem->monthly_payment;
        $system->solar_rate = $proposedSystem->solar_rate;
        $system->offset = $proposedSystem->offset;
        $system->adders = $proposedSystem->adders;
        $system->roof_work = $proposedSystem->roof_work;
        $system->external_proposal_id = $proposedSystem->external_proposal_id;
        $system->ppw = $proposedSystem->ppw;
        $system->save();

        ProposedSystem::where('lead_id', '=', $lead->id)
            ->delete();

        RequestedSystem::where('lead_id', '=', $lead->id)
            ->delete();


        if ($lead->epc_id === 1) {

            $lead->status_id = 10;
            $lead->proposed_system_id = $proposedSystem->id;

        } else {
            $lead->status_id = 6;
            $lead->proposed_system_id = $proposedSystem->id;

        }

        $lead->system_id = $system->id;
        $lead->save();

        if ($lead->epc_id === 1) {
            $now = Carbon::now()->toDateTimeString();
            $salesPacket = SalesPacket::where('id', $lead->sales_packet_id)->first();
            $salesPacket->solar_agreement_signed = $now;
            $salesPacket->cpuc_doc_signed = $now;
            $salesPacket->save();

            $salesPacketData = $salesPacket->getChanges();

            if (count($salesPacketData) > 0) {
//        I need the ID for vue to match on the page
                $something['id'] = $salesPacket->id;
                $data = collect($salesPacketData);
//        lets everyone else know of the changes
                event(new SalesPacketEvent($data, $lead->id));
            }


            $line = new Line();
            $line->requested_user_id = $user->id;
            $line->lead_id = $lead->id;
            $line->type = 'NTS';
            $line->save();


            $subject = 'NTS (' . $line->lead_id . ')';
            $body = 'Can you submit Change Order?';
            $link = URL::to('/') . '/dashboard/lead/queue/' . $line->id;
            $ntsPeoples = User::permission('edit NTS')->get();
            foreach ($ntsPeoples as $nts) {
                Mail::to($nts->email)->queue(new BaseMailable($subject, $body, $link, 'Proposal'));
            }

        }

        $leadChanges = $lead->getChanges();

        if (count($leadChanges) > 0) {
//        I need the ID for vue to match on the page

//        lets everyone else know of the changes
            event(new LeadUpdateEvent($leadChanges, $lead->id));
        }


        return $system;


    }
}
