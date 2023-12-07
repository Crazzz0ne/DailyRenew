<?php

namespace App\Console\Commands\Crons\OutsideSync\SendToAttaway;

use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Outside\ReHash;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class SendAttawayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendDataToAttaway';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs user id with sales rabbit';

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
        $leads = Lead::where('id', '>', 1)->where('stale', '!=', null)->where(function ($q) {
            $q->where('origin_office_id', 10);
            $q->orWhere('origin_office_id', 33);
            $q->orWhere('origin_office_id', 34);
            $q->orWhere('origin_office_id', 47);
            $q->orWhere('origin_office_id', 2);
        })->get();

        foreach ($leads as $lead) {
            if (!empty($lead->rehash) || !filter_var( $lead->customer->email, FILTER_VALIDATE_EMAIL )) {
                continue;
            }
            $tag = null;
            if ($lead->customer->language == 'Spanish') {
                $tag = 'spanish speaking';
            }
            $payload = [
                'external_source' => 'Scout',
                'customField' => [
                    'id' => $lead->id
                ],
                'language' => ucfirst($lead->customer->language),
                'address1' => $lead->customer->street_address,
                'city' => $lead->customer->city,
                'state' => $lead->customer->state,
                'postalCode' => $lead->customer->zip_code,
                'tags' => [$lead->statusName->name ?? 'New Lead', 'trigger_stale leads workflow', $tag],
                'firstName' => $lead->customer->first_name,
                'lastName' => $lead->customer->last_name,
                'email' => $lead->customer->email,
                'phone' => $lead->customer->cell_phone,
                'name' => $lead->customer->full_name,
            ];
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => 'https://rest.gohighlevel.com/v1/contacts/',
                // You can set any number of default request options.
                'timeout' => 500.0,
            ]);

            $apiKey = 'c06e1116-39de-4eba-8e40-a2794a70d62e';

            $client->request('post', '', [
                'headers' => [ 'Authorization' => 'Bearer ' . $apiKey ],
                'json' => $payload
            ]);
            $rehash = new ReHash();
            $rehash->name = '';
            $rehash->lead_id = $lead->id;
            $rehash->save();
        }
    }
}
