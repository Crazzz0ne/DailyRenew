<?php


namespace App\Console\Commands;


use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\WebhookClient\Models\WebhookCall;


class SyncCompleteWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:synCompleteWebhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends All Zaps';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $webhooks = WebhookCall::all();
//        dd($webhooks);
        $dumpArray = [];
        $webhookCollection = collect($webhooks);

        $i = 0;
        foreach ($webhookCollection as $webhook) {

            if (isset($webhook->payload)) {
                $payload = collect($webhook['payload']);
            } else {
                continue;
            }

            if (!isset($payload['event_type'])) {
                continue;
            }

            if ($payload['event_type'] != 'STATUS_UPDATE') {
                continue;
            }
            $lead = Lead::query();
            if (isset($payload['customer'])) {
                if (isset($payload['customer']['street'])) {
                    $lead->whereHas('customer', function ($q) use ($payload) {
                        $q->where('street_address', $payload['customer']['street']);
                        $q->orWhere('email', $payload['customer']['email']);
                    });

                } else {
                    $lead->whereHas('customer', function ($q) use ($payload) {
                        $q->where('email', $payload['customer']['email']);
                    });
                }


            } else {
                $lead->whereHas('customer', function ($q) use ($payload) {
                    $q->where('street_address', $payload['street']);
                    $q->orWhere('email', $payload['email']);
                });

            }

            $currentLead = $lead->with('customer')->first();
            if (!$currentLead) {
                continue;
            }

            if ($payload['status'] == 'SITE_SURVEY_COMPLETED') {
                $siteSurveyStartTime = Carbon::parse($payload['site_survey_date'])->addHours(6);
                $siteSurveyEndTime = Carbon::parse($payload['site_survey_date'])->addHours(6);
                $subject = 'Site Survey @ ' . $currentLead->customer->first_name . ' ' . $currentLead->customer->last_name;
                Appointment::updateOrCreate(
                    [
                        'lead_id' => $currentLead->id,
                        'type_id' => 4
                    ],
                    [
                        'user_id' => 1,
                        'subject' => $subject,
                        'start_time' => $siteSurveyStartTime->toDateTimeString(),
                        'finish_time' => $siteSurveyEndTime->toDateTimeString(),
                    ]
                );

                $salesPacket = SalesPacket::where('id', $currentLead->sales_packet_id)->first();
                $closed = Carbon::parse($salesPacket->solar_agreement_signed);
                $createdAt = Carbon::parse($webhook->created_at);
                if ($closed->greaterThan($siteSurveyStartTime)) {


                    $salesPacket->update(
                        ['solar_agreement_signed' => $createdAt->toDateTimeString(),
                            'cpuc_doc_signed' => $createdAt->toDateTimeString()
                        ]);
                }
                if ($salesPacket->cpuc_doc_signed === null) {
                    $salesPacket->update(
                        ['solar_agreement_signed' => $createdAt->toDateTimeString(),
                            'cpuc_doc_signed' => $createdAt->toDateTimeString()
                        ]);
                }

            }

            if ($payload['status'] == 'ITEMS_MISSING') {
                $lead->update(['status_id' => 16, 'jeopardy_id' => $currentLead->status_id]);
                $i++;
            }
            if ($payload['status'] == 'CANCELED') {
                $lead->update(['status_id' => 21, 'jeopardy_id' => $currentLead->status_id]);
                $i++;
            }
            if ($payload['status'] == 'JOB_IN_JEOPARDY') {
                $lead->update(['status_id' => 16, 'jeopardy_id' => $currentLead->status_id]);
                $i++;
            }
            if ($payload['status'] == 'JOB_IN_JEOPARDY') {
                $lead->update(['status_id' => 16, 'jeopardy_id' => $currentLead->status_id]);
                $i++;
            }
            if ($payload['status'] == 'JOB_OUT_OF_JEOPARDY') {
                $lead->update(['status_id' => $currentLead->jeopardy_id, 'jeopardy_id' => null]);
                $i++;
            }

            if ($payload['status'] == 'DESIGN_APPROVED') {
                $lead->update(['status_id' => 11]);
                SalesPacket::where('id', $currentLead->sales_packet_id)->update(['site_plan' => $webhook->created_at]);
                $salesPacket = SalesPacket::where('id', $currentLead->sales_packet_id)->first();
                if (!$salesPacket->submitted) {
                    $createdAt = Carbon::parse($webhook->created_at);
                    $salesPacket->update(['submitted' => $createdAt->toDateTimeString()]);
                }
                $i++;
            }
            if ($payload['status'] == 'INSTALLATION_COMPLETED') {
                $lead->update(['status_id' => 12]);
                $salesPacket = SalesPacket::where('id', $currentLead->sales_packet_id)->first();
                if (!$salesPacket->submitted) {
                    $createdAt = Carbon::parse($webhook->created_at);
                    $salesPacket->update(['submitted' => $createdAt->toDateTimeString()]);
                }
                $i++;
            }
            if ($payload['status'] == 'PTO_COMPLETED') {
                $lead->update(['status_id' => 13]);
                $salesPacket = SalesPacket::where('id', $currentLead->sales_packet_id)->first();
                if (!$salesPacket->submitted) {
                    $createdAt = Carbon::parse($webhook->created_at);
                    $salesPacket->update(['submitted' => $createdAt->toDateTimeString()]);
                }
                $i++;
            }

            if ($payload['confidence_install_date'] === "yes" && isset($payload['installation_date'])) {
                $installStartTime = Carbon::parse($payload['installation_date'])->addHours(6)->toDateTimeString();
                $installEndTime = Carbon::parse($installStartTime)->addHours(6)->toDateTimeString();
                $subject = 'Install @ ' . $currentLead->customer->first_name . ' ' . $currentLead->customer->last_name;
                Appointment::updateOrCreate(
                    [
                        'lead_id' => $currentLead->id,
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


        }



    }
}
