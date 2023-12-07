<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;

use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\ProposedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\RequestedSystemEvent;
use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Events\Backend\SalesFlow\Queue\QueuePageEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Http\Controllers\HelperController;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\Lead\RequestedSystemResource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Slack;

class RequestedSystemController
{

    public function index(Lead $lead, RequestedSystem $requestedSystem)
    {
        return RequestedSystemResource::collection(RequestedSystem::where('lead_id', $lead->id)->get());
    }


    public function create(Lead $lead, Request $request)
    {


        $requestedSystem = new RequestedSystem();
        $requestedSystem->lead_id = $lead->id;
        $requestedSystem->epc_finance_id = 395;
        $requestedSystem->solar_rate = .16;
        $requestedSystem->offset = 100;
        $requestedSystem->ppw = 4;
        $requestedSystem->save();

        $sendIt = new RequestedSystemResource($requestedSystem);

        event(new RequestedSystemEvent($sendIt, $lead->id));

        return $sendIt;

    }

    public function show(Lead $lead, RequestedSystem $requestedSystem)
    {
        return $requestedSystem;
    }

    public function update(Request $request, Lead $lead, RequestedSystem $requestedSystem)
    {
        $payload = $request->except(['userNote']);
        $requestedSystem->update($payload);
//        gets only the changes
        $something = $requestedSystem->getChanges();
        if (count($something) > 0) {
//        I need the ID for vue to match on the page
            $something['id'] = $requestedSystem->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new RequestedSystemEvent($data, $lead->id));

        }
//       I dont care about the reply outside of it being a 200
        return 'yeet';
    }

    public function request(Lead $lead, RequestedSystem $requestedSystem, Request $request)
    {

        $user = \Auth::user();

//        checks to make sure that there is only one proposed system per requested.
        $check = ProposedSystem::where('requested_system_id', $requestedSystem->id)->count();

        if (!$check) {
            $lead->jeopardy_id = null;
            if ($lead->status_id !== 9) {
                $lead->status_id = 5;
                $leadUpdate['status'] = 'Negotiating System';

            }
            $lead->save();

            $leadUpdate = $lead->getChanges();
            $leadUpdate['id'] = $lead->id;
            $data = collect($leadUpdate);
//        lets everyone else know of the changes
            event(new LeadUpdateEvent($data, $lead->id));

            $requestedSystem->approved = Carbon::now()->toDateTimeString();
            $requestedSystem->save();

            $proposedSystem = new ProposedSystem();
            $proposedSystem->lead_id = $lead->id;
            $proposedSystem->solar_rate = $requestedSystem->solar_rate;
            $proposedSystem->epc_finance_id = $requestedSystem->epc_finance_id;
            $proposedSystem->inverter_id = $requestedSystem->inverter_id;
            $proposedSystem->modules_id = $requestedSystem->modules_id;
            $proposedSystem->save();


            event(new ProposedSystemEvent($proposedSystem, $lead->id, false));

//            if ($lead->origin_office_id == 5){
//                $link = 'https://scout.solarbrightwave.com/dashboard/lead/' . $lead->id;
//                TwilioSMSController::sendSms('6199406423', 'A new lead has been assigned to you. Please log in to the portal to view it.
//                '.$link);
//                TwilioSMSController::sendSms('7609830713', 'A new lead has been assigned to you. Please log in to the portal to view it.
//                '.$link);
//            }else{
                $queue = new Line();
                $queue->requested_user_id = $user->id;
                $queue->lead_id = $lead->id;
                $queue->type = 'build_proposal';
                $queue->urgent = $request->urgent;
                $queue->save();

//            $payload = new LineResource($queue);
                $something = Line::where('id', $queue->id)->with('requestingUser', 'filledUser')->first();
                $somethingElse = new LineResource($something);
                event(new NewUserNotificationEvent($somethingElse));
                event(new QueuePageEvent($somethingElse, 'assigned', null));

//            }

            $something = $requestedSystem->getChanges();
            $something['id'] = $requestedSystem->id;
            $data = collect($something);

            event(new RequestedSystemEvent($data, $lead->id));

//            event(new NewQueueEvent($payload, 'assigned'));
        }


        return 'created';
    }

    public function changeOrder(Lead $lead, RequestedSystem $requestedSystem, Request $request)
    {

        $user = \Auth::user();

//        checks to make sure that there is only one proposed system per requested.
        $check = ProposedSystem::where('requested_system_id', $requestedSystem->id)->count();

        if (!$check) {

//            $lead->status = 'Negotiating System';
//            $lead->save();
//
//            $leadUpdate = $lead->getChanges();
//
//            $leadUpdate['id'] = $lead->id;
//            $data = collect($leadUpdate);
////        lets everyone else know of the changes
//            event(new LeadUpdateEvent($data, $lead->id));

            $requestedSystem->approved = Carbon::now()->toDateTimeString();
            $requestedSystem->save();

            $proposedSystem = new ProposedSystem();
            $proposedSystem->lead_id = $lead->id;
            $proposedSystem->epc_finance_id = $requestedSystem->epc_finance_id;
            $proposedSystem->epc_system_id = $requestedSystem->epc_system_id;
            $proposedSystem->inverter_id = $requestedSystem->inverter_id;
            $proposedSystem->modules_id = $requestedSystem->modules_id;
            $proposedSystem->modules_count = $requestedSystem->modules_count;
            $proposedSystem->requested_system_id = $requestedSystem->id;
            $proposedSystem->monthly_payment = $requestedSystem->monthly_payment;
            $proposedSystem->system_size = $requestedSystem->system_size;
            $proposedSystem->offset = $requestedSystem->offset;
            $proposedSystem->adders = $requestedSystem->adders;
            $proposedSystem->external_proposal_id = $request->external_proposal_id;
            $proposedSystem->contract_amount = $request->contract_amount;
            $proposedSystem->pb_design_approved = $user->id;
            $proposedSystem->save();

            event(new ProposedSystemEvent($proposedSystem, $lead->id, false));


            $something = $requestedSystem->getChanges();
            $something['id'] = $requestedSystem->id;
            $data = collect($something);

            event(new RequestedSystemEvent($data, $lead->id));

        }


        return 'created';
    }

}
