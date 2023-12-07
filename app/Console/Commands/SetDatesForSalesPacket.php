<?php

namespace App\Console\Commands;

use App\Handler\WebhookHandler;
use App\Http\Controllers\Controller;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\WebhookClient\Models\WebhookCall;

class SetDatesForSalesPacket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:salesPacketSync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'format Numbers Properly';

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
        foreach ($webhookCollection as $key => $webhook) {

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

            if (!isset($payload['customer'])) {
                continue;
            }
            $payload = $webhook['payload'];
            $lead = Lead::where('epc_owner_id', $payload['uuid'])->with('salesPacket', 'customer')->first();
            if (!$lead) {
                $lead = Lead::whereHas('customer', function ($q) use ($payload) {
                    if (!isset($payload['customer'])) {
                        $q->where('street_address', $payload['customer']['street_address']);

                    }
                })->with('salesPacket', 'customer')->first();


            }
            if (!$lead) {
                continue;
            }
            $createdAt = Carbon::parse($webhook['created_at'] . 'UTC')->tz('America/Los_Angeles');

            switch ($payload['status']) {
                case 'SOLAR_PERMIT_RECEIVED':
                    SalesPacket::where('id', $lead->salesPacket->id)->update(['permitting_received_date' => $createdAt]);
                    break;
                case 'SOLAR_PERMIT_APPLICATION':
                    SalesPacket::where('id', $lead->salesPacket->id)->update(['submitted_for_permitting_date' => $createdAt]);
                    break;
                case 'DESIGN_COMPLETED':
                    SalesPacket::where('id', $lead->salesPacket->id)->update(['design_plan_sent_date' => $createdAt]);
                    break;
                case 'DESIGN_APPROVED':
                    SalesPacket::where('id', $lead->salesPacket->id)->update(['site_plan' => $createdAt]);
                    break;

            }

        }

    }
}
