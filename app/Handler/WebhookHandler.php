<?php


namespace App\Handler;


use App\Events\Backend\SalesFlow\Lead\CustomerEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\SalesPacketEvent;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\WebhookClient\ProcessWebhookJob;

class WebhookHandler extends ProcessWebhookJob
{

    public function handle()
    {
        $payload = $this->webhookCall->payload;

        \Log::debug('it was at least started');
        $payloadCustomer = $payload['customer'];

        $salesPacket = null;
        if ($payload["event_type"] === "STATUS_UPDATE") {

            $customerEmail = $payloadCustomer['email'];
            $customer = Customer::where('street_address', '=', $payload['address']['street'])->get()->first();


            if (!$customer) {
                $customer = Customer::where('email', '=', $customerEmail)->get()->first();
            }
            if (!$customer) {
                $body = 'customer Scout cant find ' . $payloadCustomer['last_name'] . ' - ' . $payloadCustomer['email'] . '   ' . $payload['address']['street'];
                Mail::to('chris.furman@solcalenergy.com')
                    ->queue(new BaseMailable('customer not found', $body, 'nowhere', 'lead'));
                return null;
            }

            $lead = Lead::where('customer_id', '=', $customer->id)
                ->where('epc_id', 1)
                ->first();


            $leadId = $lead->id;
            if ($lead->id === 1 || $lead->id === 2) {
                $temp1 = $this->webhookCall;
                $temp2 = $customer;
                $temp3 = $lead;

                $array = ['webhook' => $temp1,
                    'customer' => $temp2,
                    'lead' => $temp3];


                Mail::to('chris.furman@solcalenergy.com')
                    ->queue(new BaseMailable('it did that bs again', $array, 'nowhere', 'lead'));
                return 'fail';
            }


            $customer->first_name = $payloadCustomer['first_name'];
            $customer->last_name = $payloadCustomer['last_name'];
            $customer->cell_phone = $payloadCustomer['phone'];

            if (isset($payload['address'])) {
                $customer->street_address = $payload['address']['street'];
                $customer->city = $payload['address']['city'];
                $customer->state = $payload['address']['state'];
                $customer->zip_code = $payload['address']['zip'];
            }

            $customer->save();


            if ($payload['status'] === 'ITEMS_MISSING' || $payload['status'] === 'JOB_IN_JEOPARDY') {
                $lead->jeopardy_id = $lead->status_id;
                $lead->status_id = 16;
                $lead->save();
                $leadId = $lead->id;
                $reps = User::whereHas("leads", function ($q) use ($leadId) {
                    $q->where("lead_id", "=", $leadId);
                })->get();

                if ($payload['status'] === 'ITEMS_MISSING') {
                    $reason = 'Items Missing';
                } else {
                    $reason = 'JOB_IN_JEOPARDY';
                }

                $note = new LeadNote();
                $note->note = 'JIJ: ' . $reason;
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                $subject = 'JNJ from Complete';
                $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ':  ' . $reason;
                $link = URL::to('/') . '/dashboard/lead/' . $leadId;
                $linkTitle = 'Lead';
                foreach ($reps as $rep) {
                    Mail::to($rep->email)
                        ->queue(new BaseMailable($subject, $body, $link, 'lead'));
                }

                $shane = User::where('email', '=', 'shanem@solcalenergy.com')->get()->first();
                Mail::to($shane->email)
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));
            } elseif ($payload['status'] === 'JOB_OUT_OF_JIJ') {


                $lead->status_id = $lead->jeopardy_id;
                $lead->jeopardy_id = null;
                $lead->save();

                $leadId = $lead->id;
                $reps = User::whereHas("leads", function ($q) use ($leadId) {
                    $q->where("lead_id", "=", $leadId);
                })->get();


                $note = new LeadNote();
                $note->note = 'Out of JIJ from Complete';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                $subject = 'Out of JNJ from Complete';
                $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ':  No longer in JIJ';
                $link = URL::to('/') . '/dashboard/lead/' . $leadId;
                $linkTitle = 'Lead';
                foreach ($reps as $rep) {
                    Mail::to($rep->email)
                        ->queue(new BaseMailable($subject, $body, $link, 'lead'));
                }

                $shane = User::where('email', '=', 'shanem@solcalenergy.com')->get()->first();
                Mail::to($shane->email)
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));
            }


            if ($payload['status'] === 'SP_APPROVED') {
                $salesPacket = SalesPacket::where('id', '=', $lead->sales_packet_id)->first();
                $salesPacket->converted = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $lead->status_id = 10;
                $lead->save();
                $leadId = $lead->id;

                $reps = User::whereHas("leads", function ($q) use ($leadId) {
                    $q->where("lead_id", "=", $leadId);
                })->get();

                $subject = 'Perfect Packet lead('.$leadId.')';
                $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' ';
                $link = URL::to('/') . '/dashboard/lead/' . $leadId;

                foreach ($reps as $rep) {
                    Mail::to($rep->email)
                        ->queue(new BaseMailable($subject, $body, $link, 'lead'));
                }

                $shane = User::where('email', '=', 'shanem@solcalenergy.com')->get()->first();
                Mail::to($shane->email)
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));


            } elseif ($payload['status'] === 'DESIGN_APPROVED') {
                $salesPacket = SalesPacket::where('id', '=', $lead->sales_packet_id)->first();
                $salesPacket->site_plan = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $lead->status_id = 11;
                $lead->save();

            }
            \Log::debug('it ran?');
            if (isset($payload['installation_date']) && $payload['confidence_install_date'] === "yes" &&
                $payload['status'] === 'CONFIDENCE_INSTALL_DATE') {


                $oldAppointment = Appointment::where('lead_id', $lead->id)->where('type_id', 5)->get()->count();
//
                $installStartTime = Carbon::parse($payload['installation_date'])->addHours(6)->toDateTimeString();
                $installEndTime = Carbon::parse($installStartTime)->addHours(6);
                $subject = 'Install @ ' . $customer->first_name . ' ' . $customer->last_name;
                Appointment::updateOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'type_id' => 5
                    ],
                    [
                        'user_id' => 1,
                        'subject' => $subject,
                        'start_time' => $installStartTime,
                        'finish_time' => $installEndTime,
                    ]
                );

                if (!$oldAppointment) {
                    $lead->status_id = 11;
                    $lead->save();



                    $reps = User::whereHas("leads", function ($q) use ($leadId) {
                        $q->where("lead_id", "=", $leadId);
                    })->get();

                    $subject = 'Install Date Confirmed lead('.$leadId.')';
                    $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' ';
                    $link = URL::to('/') . '/dashboard/lead/' . $leadId;

                    foreach ($reps as $rep) {
                        Mail::to($rep->email)
                            ->queue(new BaseMailable($subject, $body, $link, 'lead'));
                    }
                }
            }

            if ($payload['status'] === 'INSTALLATION_COMPLETED') {
                $lead->status_id = 12;
                $lead->save();
            }

            if ($payload['status'] === 'PTO_COMPLETED') {
                $lead->status_id = 13;
                $lead->save();
                $salesPacket = SalesPacket::where('id', '=', $lead->sales_packet_id)->first();
                $salesPacket->pto = Carbon::now()->toDateTimeString();
                $salesPacket->save();
            }


            if (isset($payload['site_survey_date'])) {


                $oldAppointment = Appointment::where('lead_id', $lead->id)->where('type_id', 4)->get()->count();

                $subject = 'Site survey @' . $customer->full_name;
                $siteSurveyStartTime = Carbon::parse($payload['site_survey_date'])->addHours(6)->toDateTimeString();
                $siteSurveyEndTime = Carbon::parse($siteSurveyStartTime)->addHours(6);

                Appointment::updateOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'type_id' => 4
                    ],
                    [
                        'user_id' => 1,
                        'subject' => $subject,
                        'start_time' => $siteSurveyStartTime,
                        'finish_time' => $siteSurveyEndTime,
                    ]
                );

                if (!$oldAppointment && ($lead->status_id === 7 || $lead->status_id === 2
                        || $lead->status_id === 1 || $lead->status_id === 3)) {
                    $lead->status_id = 8;
                    $lead->save();
                }
            }


            $leadUpdate = $lead->getChanges();
            $customerUpdate = $customer->getChanges();

            if ($salesPacket) {
                $salesPacketUpdate = $salesPacket->getChanges();
            } else {
                $salesPacketUpdate = [];
            }
            if (count($leadUpdate) > 0) {

//        I need the ID for vue to match on the page
                $leadUpdate['id'] = $lead->id;
                $data = collect($leadUpdate);
//        lets everyone else know of the changes
                event(new LeadUpdateEvent($data, $lead->id));
            }


            if (count($customerUpdate) > 0) {

                $customerUpdate['id'] = $customer->id;
                $data = collect($customerUpdate);

                event(new CustomerEvent($data, $customer->id, $lead->id));
            }

            if (count($salesPacketUpdate) > 0) {

                $salesPacketUpdate['id'] = $salesPacket->id;
                $data = collect($salesPacketUpdate);

                event(new SalesPacketEvent($data, $lead->id));
            }


//        Mail::to('chris.furman@solcalenergy.com')
//            ->queue(new BaseMailable('customer not found', $payload['email'], 'nowhere', 'lead'));



            Mail::to('chris.furman@solcalenergy.com')
                ->queue(new BaseMailable('webhook ran it did this', $this->webhookCall, 'nowhere', 'lead'));
            return $this->webhookCall;
        } else {
            Mail::to('chris.furman@solcalenergy.com')
                ->queue(new BaseMailable('webhook did not run', $this->webhookCall, 'nowhere', 'lead'));
        }
    }

}
