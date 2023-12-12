<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\CustomerSatEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\Note\NewNoteEvent;
use App\Events\Backend\SalesFlow\Lead\ProposedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\RequestedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\SalesPacketEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Events\Backend\SalesFlow\Queue\QueuePageEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\Lead\RequestedSystemResource;
use App\Http\Resources\Lead\SalesPacketResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadStatus;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Slack;

class SalesPacketController
{

    protected $leadRepository;

    public function __construct(Lead $lead)
    {
        // set the model
        $this->leadRepository = new LeadRepository();
    }

    public function index()
    {

    }

    public function show(Lead $lead, $id, Request $request)
    {
        $salesPacket = SalesPacket::where('id', $lead->sales_packet_id)->with('lead')->first();
        return new SalesPacketResource($salesPacket);
    }

    public function update(Lead $lead, $id, Request $request)
    {
        $apiUser = \Auth::user();

        $today = Carbon::now();
        $requestKeys = $request->keys();
        $salesPacket = SalesPacket::where('id', $lead->sales_packet_id)->first();

        switch ($requestKeys[0]) {
            case 'ach_doc_signed':
                if (!$salesPacket->ach_doc_signed) {
                    $salesPacket->ach_doc_signed = $today;
                    $something['ach_doc_signed'] = true;
                } else {
                    $salesPacket->ach_doc_signed = null;
                    $something['ach_doc_signed'] = false;
                }
                break;
            case 'converted':
                if (!$salesPacket->converted) {
                    $salesPacket->converted = $today;
                    $something = true;
                } else {
                    $salesPacket->converted = null;
                    $something = false;
                }
                break;
            case 'site_plan':
                if (!$salesPacket->site_plan) {
                    $salesPacket->site_plan = $today;
                    $something = true;
                } else {
                    $salesPacket->site_plan = null;
                    $something = false;
                }
                break;
            case 'pto':
                if (!$salesPacket->pto) {
                    $salesPacket->pto = $today;
                    $something['pto'] = true;
                } else {
                    $salesPacket->pto = null;
                    $something = false;
                }
                break;
            case 'site_survey_note':
                $salesPacket->site_survey_note = $request->site_survey_note;
                break;
            case 'submitted':
                if (!$salesPacket->submitted) {
                    $salesPacket->submitted = $today;
                    $something['pto'] = true;
                } else {
                    $salesPacket->submitted = null;
                    $something = false;
                }
                break;
            case 'sat':
                $salesPacket->sat = $request->sat;

                event(new UpdateZapierEvent($lead, 'sit'));
                break;
            default:
                $something = $salesPacket->getChanges();
                break;
        }


        $salesPacket->save();
        $something = $salesPacket->getChanges();
//        gets only the changes

        if (count($something) > 0) {
            if ($request->sat) {
                event(new CustomerSatEvent($lead, Auth::user()));
            }

//        I need the ID for vue to match on the page
            $something['id'] = $salesPacket->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new SalesPacketEvent($data, $lead->id));
        }
//       I dont care about the reply outside of it being a 200
        return $something;

    }

    public function cancelChangeOrder(Lead $lead, $id, Request $request){
        ProposedSystem::where('lead_id',  $lead->id)
            ->delete();

        RequestedSystem::where('lead_id',  $lead->id)
            ->delete();
         Line::where('lead_id', $lead->id)->where('type', 'change_order')->delete();


        $audit = $lead->audits()->latest()->get();
        if ($audit->count() === 0) {
            $lead->status_id = $audit[0]->old_values['status_id'];

        }else{
            $lead->status_id = 11;
        }
        $lead->save();

        $leadUpdate = $lead->getChanges();
        $statusName = LeadStatus::where('id', $lead->status_id)->first();
        $leadUpdate['id'] = $lead->id;
        $leadUpdate['status'] = $statusName->name;

        $data = collect($leadUpdate);
        event(new LeadUpdateEvent($data, $lead->id));
    }

    public function createChangeOrder(Lead $lead, $id, Request $request)
    {

        $user = Auth::user();


        $lead->status_id = 9;
        $lead->save();

        $leadUpdate = $lead->getChanges();

        $leadUpdate['id'] = $lead->id;
        $leadUpdate['status'] = 'Change Order';
        $data = collect($leadUpdate);
//        lets everyone else know of the changes

        $requestedSystem = new RequestedSystem();
        $requestedSystem->lead_id = $lead->id;
        $requestedSystem->inverter_id = $lead->system->inverter_id ?? 0;
        $requestedSystem->modules_id = $lead->system->modules_id ?? 0;
        $requestedSystem->epc_finance_id = $lead->system->epc_finance_id ;
        $requestedSystem->save();

//        delete line of lead
        Line::where('lead_id', $lead->id)->where('type', 'sun_run_runner')->delete();

        event(new RequestedSystemEvent($requestedSystem, $lead->id, false));

        event(new LeadUpdateEvent($data, $lead->id));

        $notes = 'Created a Change order.';
        $note = new LeadNote();
        $note->lead_id = $lead->id;
        $note->user_id = $user->id;
        $note->note = $notes;
        $note->save();

        broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, false));
        event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, false));


    }
}
