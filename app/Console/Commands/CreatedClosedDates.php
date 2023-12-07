<?php


namespace App\Console\Commands;


use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreatedClosedDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createdClosed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adds 30 people to mailchimp';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    ///TODO: this has breaking bugs don't use, Appointment needs to be updated.

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $webCalls = \Spatie\WebhookClient\Models\WebhookCall::where('name', 'complete')->get();
        $i = 0;

        $array = [];
        foreach ($webCalls as $web) {
            array_push($array, $web->id);
            if (isset($web['payload']['email'])) {
                $email = $web['payload']['email'];
            } elseif (isset($web->payload['customer']['email'])) {
                $email = $web['payload']['customer']['email'];


            } else {

            }

            if (isset($web['payload']['street'])) {
                $street = $web['payload']['street'];
            } elseif (isset($web['payload']['address']['street'])) {
                $street = $web['payload']['address']['street'];
            } else {
                $street = null;
            }

            if (!$street) {
                continue;
            }


//            if ($street === '36851 96th Street East'){
//                return 'hello';
//            }
            if (isset($web['payload']['email']) || isset($web['payload']['customer']['email'])) {

                if ($web['payload']['event_type'] === 'STATUS_UPDATE') {


//                    dump($email);

                    $customer = Customer::where('street_address', $street)
                        ->orWhere('email', $email)
                        ->first();

                    if (!$customer) {
                        continue;
                    }


                    $lead = Lead::where('customer_id', $customer->id)->first();


                    if (!$lead) {
                        break;
                    }
                    if ($lead->id === 2583) {
                        dump('here');
                    }
                    $salesPacket = SalesPacket::where('id', $lead->sales_packet_id)->first();


                    switch ($web['payload']['status']) {
                        case 'SP_APPROVED':
                            if (!$lead->close_date) {
                                $lead->close_date = $web['created_at'];
                            }
                            $lead->status_id = 10;
                            if (!$salesPacket->solar_agreement_signed) {
                                $salesPacket->solar_agreement_signed = $web['created_at'];
                            }
                            if (!$salesPacket->cpuc_doc_signed = $web['created_at']) {
                                $salesPacket->cpuc_doc_signed = $web['created_at'];
                            }


                            break;
                        case 'DESIGN_APPROVED':
                            if (!$lead->close_date) {
                                $lead->close_date = $web['created_at'];
                            }
                            if (!$salesPacket->solar_agreement_signed) {
                                $salesPacket->solar_agreement_signed = $web['created_at'];
                            }
                            if (!$salesPacket->cpuc_doc_signed) {
                                $salesPacket->cpuc_doc_signed = $web['created_at'];
                            }
                            $lead->status_id = 11;


                            break;
                        case 'CONFIDENCE_INSTALL_DATE':

                            if (!$lead->close_date) {
                                $lead->close_date = $web['created_at'];
                            }
                            if (!$salesPacket->solar_agreement_signed) {
                                $salesPacket->solar_agreement_signed = $web['created_at'];
                            }
                            if (!$salesPacket->cpuc_doc_signed) {
                                $salesPacket->cpuc_doc_signed = $web['created_at'];
                            }
                            $lead->status_id = 11;

                            break;
                        case 'INSTALLATION_COMPLETED':
                            $lead->status_id = 12;
                            if (!$lead->close_date) {
                                $lead->close_date = $web['created_at'];
                            }
                            if (!$salesPacket->solar_agreement_signed) {
                                $salesPacket->solar_agreement_signed = $web['created_at'];
                            }
                            if (!$salesPacket->cpuc_doc_signed) {
                                $salesPacket->cpuc_doc_signed = $web['created_at'];
                            }

                            break;
                        case 'PTO_COMPLETED':
                            $lead->status_id = 13;
                            if (!$lead->close_date) {
                                $lead->close_date = $web['created_at'];
                            }
                            if (!$salesPacket->solar_agreement_signed) {
                                $salesPacket->solar_agreement_signed = $web['created_at'];
                            }
                            if (!$salesPacket->cpuc_doc_signed) {
                                $salesPacket->cpuc_doc_signed = $web['created_at'];
                            }

                            break;
                        case 'SITE_SURVEY_COMPLETED':
                            $lead->close_date = $web['created_at'];
                            if (!$lead->close_date) {
                                $lead->close_date = $web['created_at'];
                            }
                            if (!$salesPacket->solar_agreement_signed) {
                                $salesPacket->solar_agreement_signed = $web['created_at'];
                            }
                            if (!$salesPacket->cpuc_doc_signed) {
                                $salesPacket->cpuc_doc_signed = $web['created_at'];
                            }
                            break;
                        default:

                            break;
                    }
                    $salesPacket->sat = 1;

                    $salesPacket->save();


                    $lead->save();

                    if (isset($web['payload']['installation_date'])) {
                        $installStartTime = Carbon::parse($web['payload']['installation_date'])->addHours(6)->toDateTimeString();
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
                    }

                    if (isset($web['payload']['installation_date'])) {
                        $subject = 'Site survey @' . $customer->full_name;
                        $siteSurveyStartTime = Carbon::parse($web['payload']['site_survey_date'])->addHours(6)->toDateTimeString();
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
                    }

                }

            }
            $i++;
        }
        dump($array);
        dump($i);
    }
}
