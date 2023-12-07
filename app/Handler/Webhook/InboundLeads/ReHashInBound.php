<?php

namespace App\Handler\Webhook\InboundLeads;

use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\QueuePageEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Http\Resources\Lead\LineResource;
use App\Models\Auth\User;
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

class ReHashInBound extends ProcessWebhookJob
{

    public function handle()
    {

        $payload = $this->webhookCall->payload;

        $temps = collect($payload);
//        dd(gettype($temp));

//      $test =   json_decode($payload, true);
        $arrayBykey = [];
        foreach ($temps as $key => $value) {
            $arrayBykey[] = $value;
        }


        $payload = collect($payload);

        if (isset($payload['phone'])) {
            if (substr($payload['phone'], 0, 1) === '+') {
                $cell = trim($payload['phone'], '+1');
            } elseif (substr($payload['phone'], 0, 1) === '1') {
                $cell = ltrim($payload['phone'], $payload['phone'][0]);
            } else {
                $cell = $payload['phone'];
            }
        }

        $customer = new Customer();
        $customer->first_name = $payload['first_name'] ?? '';
        $customer->last_name = $payload['last_name'] ?? '';
        $customer->email = $payload['email'] ?? '';
        $customer->cell_phone = $cell ?? '';
        $customer->street_address = $payload['address1'];
        $customer->city = $payload['city'];
        $customer->state = $payload['state'];
        $customer->zip_code = $payload['postal_code'] ?? '';

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

        $user = User::where('email', $payload['user']['email'])->first();

        $newUser = new UserHasLead();
        $newUser->user_id = $user->id;
        $newUser->lead_id = $lead->id;
        $newUser->position_id = 1;
        $newUser->save();

        $newUser = new UserHasLead();
        $newUser->user_id = $user->id;
        $newUser->lead_id = $lead->id;
        $newUser->position_id = 2;
        $newUser->save();

        $note = new LeadNote();
        $note->note = $payload["Disco Call Booking Notes"];
        $note->user_id = 1;
        $note->lead_id = $lead->id;
        $note->save();

        $note = new LeadNote();
        $note->note = 'Tags: ' . $payload["tags"];
        $note->user_id = 478;
        $note->lead_id = $lead->id;
        $note->save();

    }


}
