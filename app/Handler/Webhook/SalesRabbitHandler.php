<?php


namespace App\Handler\Webhook;


use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\QueuePageEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Http\Controllers\HelperController;
use App\Http\Resources\Lead\LineResource;
use App\Models\Auth\User;
use App\Models\Epc\Epc;
use App\Models\Office\Office;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\UserHasLead;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\WebhookClient\ProcessWebhookJob;

class SalesRabbitHandler extends ProcessWebhookJob
{

    public function handle()
    {

        $payload = $this->webhookCall->payload;
        $data = (object)$payload['leadData'];

        $user = User::whereHas('apiKey', function ($q) use ($data) {
            $q->where('type', 'sales-rabbit');
            $q->where('api_key', $data->userId);
        })->first();

        if (!$user) {
            dd('nope');
        }

//       Customer::where('email', $data->email)
//           ->orwhere('street_address', $data->street_address)
//           ->get();
        $customer = Customer::where('street_address', $data->street1)->whereHas('leads', function ($q) use ($user) {

            $q->whereHas('reps', function ($qs) use ($user) {
                $qs->where('user_id', $user->id);
            });
        })->with('leads.reps')->get();

        if ($customer->count()) {
//            dd($customer[0]->leads[0]->id);
            $body = 'You have a lead for this customer already request in scout click the link to access: ' . $customer[0]->leads[0]->id .
                ' scout.solar/dashboard/lead/' . $customer[0]->leads[0]->id;

            $sp1Count = UserHasLead::where('lead_id', $customer[0]->leads[0]->id)
                ->where('type', 'sp1')
                ->count();
            $sp1QueueCount = Line::where('lead_id', $customer[0]->leads[0]->id)
                ->where('type', 'sp1')
                ->count();

            if ($sp1Count || $sp1QueueCount){
                $body = 'You have a lead for this customer already request in Scout, click the link to access: ' . $customer[0]->leads[0]->id .
                    ' scout.solar/dashboard/lead/' . $customer[0]->leads[0]->id;
            }else{
                $body = 'You have a lead for this customer already request in Scout, sp1 has been requested click the link to access: ' . $customer[0]->leads[0]->id .
                    ' scout.solar/dashboard/lead/' . $customer[0]->leads[0]->id;

            TwilioSMSController::sendSms($user->phone, $body);
            $line = new Line();
            $line->requested_user_id = $user->id;
            $line->lead_id = $customer[0]->leads[0]->id;
            $line->type = 'sp1';
            $line->save();

            $something = Line::where('id', $line->id)->with('requestingUser', 'filledUser')->first();
            $somethingElse = new LineResource($something);
            event(new NewUserNotificationEvent($somethingElse));
            event(new QueuePageEvent($somethingElse, 'assigned', null));
            event(new ElevatorEvent($line->type, 1));
            }
            die('fin');
        }

        $customer = new Customer();
        $customer->first_name = $data->firstName ?? 'not set';
        $customer->last_name = $data->lastName ?? 'not set';
        $customer->email = $data->email ?? 'not set';
        $customer->cell_phone = $data->phonePrimary ?? 'not set';
        $customer->street_address = $data->street1;
        $customer->zip_code = $data->postalCode;
        $customer->state = $data->state;
        $customer->language = 'english';
        $customer->save();


        $powerCompany = Epc::where('id', 1)
            ->with('powerCompany')
            ->get()
            ->pluck('powerCompany.id');

        $utility = new LeadUtility();
        if (isset($powerCompany[0])) {
            $utility->power_company_id = $powerCompany[0];
        }
        $utility->save();
        $salesPacket = new SalesPacket();
        $salesPacket->save();

        if (app()->environment('production')) {
            $finOption = 10;
            $creditStatus = 1;
        } else {
            $finOption = 1;
            $creditStatus = 1;
        }

        if ($user->hasRole('canvasser')) {
            $source = 'canvasser';
        } else {
            $source = 'self gen';
        }

        $lead = new Lead();
        $lead->credit_status_id = $creditStatus;
        $lead->office_id = $user->office_id;
        $lead->origin_office_id = $user->office_id;
        $lead->customer_id = $customer->id;
        $lead->sales_packet_id = $salesPacket->id;
        $lead->utility_id = $utility->id;
        $lead->source = $source;
        $lead->epc_id = 1;
        $lead->save();


        $office = Office::where('id', $user->office_id)->first();


//        $proposal = new RequestedSystem();
//        $proposal->epc_finance_id = $finOption;
//        $proposal->ppw = $office->default_ppw;
//        $proposal->offset = 100;
//        $proposal->solar_rate = .18;
//        $proposal->lead_id = $lead->id;
//        $proposal->save();

        $leadLogin = new LeadLogin();
        $leadLogin->lead_id = $lead->id;
        $leadLogin->user_name = ' ';
        $leadLogin->password = ' ';
        $leadLogin->save();

        if ($office->require_integrations) {
            $lead->integrations_approved = 1;
        } else {
            $lead->integrations_approved = 3;
        }
        $lead->save();

        $rep = new UserHasLead();
        $rep->user_id = $user->id;
        $rep->lead_id = $lead->id;
        $rep->position_id = 1;
        $rep->save();

        if ($user->hasPermissionTo('accept sp1')) {
            $rep = new UserHasLead();
            $rep->user_id = $user->id;
            $rep->lead_id = $lead->id;
            $rep->position_id = 2;
            $rep->save();
        } else {
            $line = new Line();
            $line->requested_user_id = $user->id;
            $line->lead_id = $lead->id;
            $line->type = 'sp1';
            $line->save();

            $something = Line::where('id', $line->id)->with('requestingUser', 'filledUser')->first();
            $somethingElse = new LineResource($something);
            event(new NewUserNotificationEvent($somethingElse));
            event(new QueuePageEvent($somethingElse, 'assigned', null));
            event(new ElevatorEvent($line->type, 1));
        }

        $customer = Customer::where('street_address', $data->street1)->whereHas('leads', function ($q) use ($user) {

            $q->whereHas('reps', function ($qs) use ($user) {
                $qs->where('user_id', $user->id);
            });
        })->with('leads.reps')->get();
        $body = 'You have a lead for ' . $customer[0]->first_name . ' click the link to access: ' . $lead->id .
            ' scout.solar/dashboard/lead/' . $customer[0]->leads[0]->id;
        TwilioSMSController::sendSms($user->phone, $body);

    }
}
