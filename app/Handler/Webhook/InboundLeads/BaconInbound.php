<?php

namespace App\Handler\Webhook\InboundLeads;


use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\QueuePageEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Http\Resources\Lead\LineResource;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\UserHasLead;
use Spatie\WebhookClient\ProcessWebhookJob;

class BaconInbound extends ProcessWebhookJob
{

    public function handle()
    {
        die();
        $payload = $this->webhookCall->payload;

        $temps = collect($payload);
//        dd(gettype($temp));

//      $test =   json_decode($payload, true);
        $arrayBykey = [];
        foreach ($temps as $key => $value) {
            $arrayBykey[] = $value;
        }
        $leads = Lead::where('intake_id', $payload['contact_id'])->get();
        if (!$leads->count()) {

            $payload = collect($payload);
            $address = preg_split("/\,/", $payload['address1']);

            if (isset($address[1])){
                $streetAddress =  $address[0];
                $city =  $address[1];
            }else{
                $streetAddress ='';
                $city = '';
            }

            if (isset($payload['phone'])){
                if (substr($payload['phone'], 0,1) === '+'){
                 $cell =   trim($payload['phone'], '+1');
                }elseif (substr($payload['phone'], 0,1) === '1'){
                    $cell =   ltrim($payload['phone'], $payload['phone'][0]);
                }else{
                    $cell = $payload['phone'];
                }
            }

            $customer = new Customer();
            $customer->first_name = $payload['first_name'] ?? '';
            $customer->last_name = $payload['last_name'] ?? '';
            $customer->email = $payload['email'] ?? '';
            $customer->cell_phone = $cell ?? '';
            $customer->street_address = $streetAddress ?? '';
            $customer->city = $city ?? '';
            $customer->state = 'CA';
            $customer->zip_code = $payload['postal_code'] ?? '';
            $customer->language = strtolower($payload["Language"]) ?? '';
            $customer->save();

            $utility = new LeadUtility();
            if (isset($powerCompany[0])) {
                $utility->power_company_id = $powerCompany[0];
            }
            $utility->save();
            $salesPacket = new SalesPacket();
            $salesPacket->save();
            //Hard code from database.

            $finOption = 10;
            $creditStatus = 1;


            $officeId = 43;
            $lead = new Lead();
            $lead->credit_status_id = $creditStatus;
            $lead->integrations_approved = 1;
            $lead->source = 'external';
            $lead->office_id = $officeId;
            $lead->origin_office_id = $officeId;
            $lead->customer_id = $customer->id;
            $lead->sales_packet_id = $salesPacket->id;
            $lead->utility_id = $utility->id;
            $lead->epc_id = 1;
            $lead->intake_id = $payload['contact_id'];
            $lead->save();

            $proposal = new RequestedSystem();
            $proposal->epc_finance_id = 8;
            $proposal->ppw = null;
            $proposal->offset = 110;
            $proposal->solar_rate = .16;
            $proposal->lead_id = $lead->id;
            $proposal->save();


            $leadLogin = new LeadLogin();
            $leadLogin->lead_id = $lead->id;
            $leadLogin->user_name = ' ';
            $leadLogin->password = ' ';
            $leadLogin->save();

            $line = new Line();
            $line->requested_user_id = 478;
            $line->lead_id = $lead->id;
            $line->type = 'sp1';
            $line->save();

            $something = Line::where('id', $line->id)
                ->with('requestingUser', 'filledUser')
                ->first();
            $somethingElse = new LineResource($something);
//        broadcast(new NewQueueEvent($somethingElse, 'assigned', null, $user));
            event(new NewUserNotificationEvent($somethingElse));
            event(new QueuePageEvent($somethingElse, 'assigned', null));
            event(new ElevatorEvent($line->type, 1));

            $note = new LeadNote();
            $note->note = 'What Type Of Home Do You Own? ' . $arrayBykey[29];
            $note->user_id = 478;
            $note->lead_id = $lead->id;
            $note->save();

            $note = new LeadNote();
            $note->note = 'Next, How Much Sun Does Your Roof Get? ' . $arrayBykey[30];
            $note->user_id = 478;
            $note->lead_id = $lead->id;
            $note->save();

            $note = new LeadNote();
            $note->note = 'What is your credit score? ' . $arrayBykey[28];
            $note->user_id = 478;
            $note->lead_id = $lead->id;
            $note->save();

            $note = new LeadNote();
            $note->note = 'What\'s your average electric bill? ' . $arrayBykey[31];
            $note->user_id = 478;
            $note->lead_id = $lead->id;
            $note->save();

            $newUser = new UserHasLead();
            $newUser->user_id = 478;
            $newUser->lead_id = $lead->id;
            $newUser->position_id = 1;
            $newUser->save();

            if (!isset($address[1])){
                $note = new LeadNote();
                $note->note = 'address    ' . $address[0];
                $note->user_id = 478;
                $note->lead_id = $lead->id;
                $note->save();
            }

        } else {

            foreach ($leads as $lead) {
                $note = new LeadNote();
                $note->note = $payload['calendar']['title'] . ' .
                start time :' . $payload['calendar']['startTime'];
                $note->user_id = 478;
                $note->lead_id = $lead->id;
                $note->save();
            }
        }
    }

}
