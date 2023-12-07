<?php


namespace App\Handler\Webhook;


use App\Events\Backend\SalesFlow\Lead\CustomerEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\Note\NewNoteEvent;
use App\Events\Backend\SalesFlow\Lead\SalesPacketEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\WebhookClient\ProcessWebhookJob;

class CompleteHandler extends ProcessWebhookJob
{

    public function handle()
    {

        $payload = $this->webhookCall->payload;
        if ($payload["event_type"] !== "STATUS_UPDATE") {
            return null;
        }

//        \Log::debug('it was at least started');
        $payloadCustomer = $payload['customer'];


        if ($payload["event_type"] === "STATUS_UPDATE") {


            $lead = Lead::where('epc_owner_id', $payload['uuid'])->firstOr(function () {
                return null;
            });
            if ($lead === null) {
                $lead = Lead::where('id', $payload['external_id'])->firstOr(function () {
                    return null;
                });
            }

//dd($lead);
            if ($lead === null) {
                $customerEmail = $payloadCustomer['email'];
                $customer = Customer::where('street_address', $payload['address']['street'])->firstOr(function () {
                    return null;
                });

                if ($customer === null) {
                    $customer = Customer::where('email', $customerEmail)->firstOr(function () {
                        return null;
                    });
                }


                if ($customer === null) {
                    $body = 'customer Scout cant find ' . $payloadCustomer['last_name'] . ' - ' . $payloadCustomer['email'] . '   ' . $payload['address']['street'];
                    Mail::to('proposals@solcalenergy.com')
                        ->queue(new BaseMailable('customer not found', $body, 'nowhere', 'lead'));
                    return null;
                }

                $lead = Lead::where('customer_id', $customer->id)
                    ->where('epc_id', 1)
                    ->first();
                if (!$lead) {
                    $body = 'Lead not Found Scout cant find ' . $payloadCustomer['last_name'] . ' - ' . $payloadCustomer['email'] . '   ' . $payload['address']['street'];
                    Mail::to('chris.furman@solcalenergy.com')
                        ->queue(new BaseMailable('Lead not found', $body, 'nowhere', 'lead'));
                }

            } else {
                $customer = Customer::where('id', $lead->customer_id)->first();

            }
            $salesPacket = SalesPacket::where('id', '=', $lead->sales_packet_id)->first();
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
            $link = URL::to('/') . '/dashboard/lead/' . $leadId;
            SalesPacket::where('id', $lead->sales_packet_id)->update(['sat' => true]);


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
                    $q->where("lead_id", $leadId);
                })->get();

                if ($payload['status'] === 'ITEMS_MISSING') {
                    $reason = 'Items Missing';
                } else {
                    $reason = 'Job In Jeopardy';
                }

                $note = new LeadNote();
                $note->note = 'JIJ: ' . $reason;
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, true));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, true));

                $subject = 'JNJ from Complete';
                $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ':  ' . $reason;
                $link = URL::to('/') . '/dashboard/lead/' . $leadId;
                $linkTitle = 'Lead';

//                $accountManagers = User::role('account manager')->get();
//
//                foreach ($accountManagers as $manager) {
//                    Mail::to($manager->email)
//                        ->queue(new BaseMailable($subject, $body, $link, 'lead'));
//                }
                $shane = User::where('email', 'shane@solarbrightwave.com')->first();
                Mail::to($shane->email)
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));
                Mail::to('chris.furman@solcalenergy.com')
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));
            } elseif ($payload['status'] === 'JOB_OUT_OF_JEOPARDY') {


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

                $link = URL::to('/') . '/dashboard/lead/' . $leadId;


//                $accountManagers = User::role('account manager')->get();
//
//                foreach ($accountManagers as $manager) {
//                    Mail::to($manager->email)
//                        ->queue(new BaseMailable($subject, $body, $link, 'lead'));
//                }

                $shane = User::where('email', '=', 'shane@solarbrightwave.com')->first();

                $subject = 'Out of JNJ from Complete';
                $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ':  No longer in JIJ';
                Mail::to($shane->email)
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));

            }


            if ($payload['status'] === 'CANCELED') {

                $lead->jeopardy_id = $lead->status_id;
                $lead->status_id = 21;
                $lead->save();
                $leadId = $lead->id;

                $note = new LeadNote();
                $note->note = 'Customer Canceled 😭';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                $subject = 'Customer ' . $customer->last_name . ' Canceled';
                $body = 'Complete has reported that  ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' Has Canceled';
                $link = URL::to('/') . '/dashboard/lead/' . $leadId;
                Appointment::where('lead_id', $leadId)
                    ->where('type_id',5 )
                    ->update(['status_id' => 2, 'completed_at' => Carbon::now()->toDateTimeString()]);

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, true));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, true));

//                $accountManagers = User::role('account manager')->get();
//
//                foreach ($accountManagers as $manager) {
//                    Mail::to($manager->email)
//                        ->queue(new BaseMailable($subject, $body, $link, 'lead'));
//                }


                $shane = User::where('email', '=', 'shane@solarbrightwave.com')->first();
                Mail::to($shane->email)
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));

            }

            if ($payload['status'] === 'SP_APPROVED') {

                $salesPacket->converted = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $lead->status_id = 11;
                $lead->jeopardy_id = null;
                $lead->save();
                $leadId = $lead->id;


                $subject = 'Perfect Packet lead(' . $leadId . ')';
                $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name .
                    '. All of your paperwork has been submitted properly. Next up design plan';
                $link = URL::to('/') . '/dashboard/lead/' . $leadId;

                $noteBody = 'We have a Perfect Packet! All of the paper work looks great, thank you!';

                $note = new LeadNote();
                $note->note = $noteBody;
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, false));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, false));


//                $accountManagers = User::role('account manager')->get();
//
//                foreach ($accountManagers as $manager) {
//                    Mail::to($manager->email)
//                        ->queue(new BaseMailable($subject, $body, $link, 'lead'));
//                }

                $shane = User::where('email', 'shane@solarbrightwave.com')->first();
                Mail::to($shane->email)
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));

            }

            if ($payload['status'] === 'DESIGN_COMPLETED') {

                $salesPacket->design_plan_sent_date = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $note = new LeadNote();
                $note->note = 'Design sent to customer for approval, Please remind your customer to reply "Approved" to the email.';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();


                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, true));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, true));


//                Maybe we want to send to everyone.
//                $subject = 'Customer ' . $customer->last_name . ' Canceled';
//                $body = 'Complete has reported that  ' . $lead->customer->first_name . ' ' . $lead->customer->last_name . ' Has Canceled';
//                $link = URL::to('/') . '/dashboard/lead/' . $leadId;
//
//                foreach ($reps as $rep) {
//                    Mail::to($rep->email)
//                        ->queue(new BaseMailable($subject, $body, $link, 'Lead'));
//                }
            }



            if ($payload['status'] === 'DESIGN_APPROVED') {
                $salesPacket->site_plan = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $note = new LeadNote();
                $note->note = 'Design is now approved!';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, false));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, false));

                $salesPacket->site_plan = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $lead->status_id = 11;
                $lead->jeopardy_id = null;
                $lead->save();




            }

            if ($payload['status'] === 'SOLAR_PERMIT_APPLICATION') {

                $salesPacket->submitted_for_permitting_date = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $note = new LeadNote();
                $note->note = 'Submitted For Permitting!';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, true));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, true));

                $salesPacket->site_plan = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $lead->status_id = 11;
                $lead->jeopardy_id = null;
                $lead->save();

            }

            if ($payload['status'] === 'SOLAR_PERMIT_RECEIVED') {

                $salesPacket->permitting_received_date = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $note = new LeadNote();
                $note->note = 'Solar permit has been received, awaiting install date';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, true));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, true));

                $salesPacket->site_plan = Carbon::now()->toDateTimeString();
                $salesPacket->save();
                $lead->status_id = 11;
                $lead->jeopardy_id = null;
                $lead->save();



            }



            if (isset($payload['installation_date']) && $payload['status'] === 'CONFIDENCE_INSTALL_DATE') {





                $installStartTime = Carbon::parse($payload['installation_date'])->hour(13)->toDateTimeString();
                $installEndTime = Carbon::parse($payload['installation_date'])->hour(16)->toDateTimeString();
                $subject = 'Install @ ' . $customer->first_name . ' ' . $customer->last_name;
                Appointment::updateOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'type_id' => 5
                    ],
                    [
                        'user_id' => 1,
                        'created_by' => 1,
                        'subject' => $subject,
                        'status_id' => 1,
                        'start_time' => $installStartTime,
                        'finish_time' => $installEndTime,
                    ]
                );
                $lead->status_id = 11;
                $lead->jeopardy_id = null;
                $lead->save();

                $note = new LeadNote();
                $note->note = 'We have a new Install Date! ' . Carbon::parse($installStartTime)->toDateString();
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();


                    broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, false));
                    event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, false));


            }

            if ($payload['status'] === 'INSTALLATION_COMPLETED') {
                $lead->status_id = 12;
                $lead->jeopardy_id = null;
                $note = new LeadNote();
                $note->note = 'Install Is completed! ';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();
                $lead->save();

               Appointment::where('lead_id', $lead->id)
                   ->where('type_id', 5)
                   ->update(['status_id' => 3, 'completed_at' => Carbon::now()->toDateTimeString()]);

                event(new UpdateZapierEvent($lead, 'installed'));


                $subject = 'New Install Completed(' . $leadId . ')';
                $body = 'For customer ' . $lead->customer->first_name . ' ' . $lead->customer->last_name .
                    '. ';
                $link = URL::to('/') . '/dashboard/lead/' . $leadId;

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, true));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, true));

                Mail::to('chrisfurman86@gmail.com')
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));

                Mail::to('shane@solarbrightwave.com')
                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));
            }

            if ($payload['status'] === 'PTO_COMPLETED') {
                $lead->status_id = 13;
                $lead->jeopardy_id = null;
                $note = new LeadNote();
                $note->note = 'We have PTO  🚀';
                $note->lead_id = $lead->id;
                $note->user_id = 1;
                $note->save();

                broadcast(new LeadNewMessageEvent($lead->id, $note->id, $note->note, 1, false));
                event(new NewNoteEvent($lead->id, $note->id, $note->note, 1, false));

                $lead->save();
                $salesPacket = SalesPacket::where('id', '=', $lead->sales_packet_id)->first();
                $salesPacket->pto = Carbon::now()->toDateTimeString();
                $salesPacket->save();
            }

// We have another script that does this now...
            if (isset($payload['site_survey_date'])) {


//                $oldAppointment = Appointment::where('lead_id', $lead->id)->where('type_id', 4)->get()->count();

                $subject = 'Site survey @' . $customer->full_name;
                $siteSurveyStartTime = Carbon::parse($payload['site_survey_date'], 'America/Los_Angeles')->setTimezone('UTC')->toDateTimeString();
                $siteSurveyEndTime = Carbon::parse($payload['site_survey_date'], 'America/Los_Angeles')->setTimezone('UTC')->addHours(2);

                Appointment::updateOrCreate(
                    [
                        'lead_id' => $lead->id,
                        'type_id' => 4
                    ],
                    [
                        'user_id' => 1,
                        'created_by' => 1,
                        'subject' => $subject,
                        'start_time' => $siteSurveyStartTime,
                        'finish_time' => $siteSurveyEndTime,
                    ]
                );


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



        } else {
            Mail::to('chris.furman@solcalenergy.com')
                ->queue(new BaseMailable('webhook did not run', $this->webhookCall, 'nowhere', 'lead'));
        }
    }

}
