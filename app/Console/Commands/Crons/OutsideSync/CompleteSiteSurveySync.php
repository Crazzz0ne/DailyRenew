<?php

namespace App\Console\Commands\Crons\OutsideSync;

use App\Models\Auth\User;
use App\Models\Auth\UserApi;
use App\Models\Office\Market\RegionAPI;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CompleteSiteSurveySync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncCompleteSiteSurvey';

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
        $baseURL = 'https://heliotrack.completesolar.com';
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $baseURL,
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);
        $leads = Lead::where('epc_owner_id', '!=', null)
            ->whereDoesntHave('appointments', function ($q) {
                $q->where('type_id', '=', '4');
            })->get();


        $i = 0;
        foreach ($leads as $lead) {
            sleep(5);
//            dump($lead->id, $lead->epc_owner_id);
            $key = $this->getKey($lead);

            try {
                $response = $client->request('GET', '/api/customers/'.$lead->epc_owner_id, [
                    'headers' => [
                        'x-api-key' => $key
                    ],
                ]);
                $body = json_decode($response->getBody()->getContents());
//        dd($body);
            } catch (ClientException | \Exception $e) {
                $response = $e->getResponse();
                Log::alert('Error response from complete:  ' . $response);
            }

            if (!empty($body->data)) {
//                Log::alert('Error response from complete:  ' . $body->data);
                if ($body->data[0]->site_survey_date !== null) {
                    $startTime = Carbon::parse( $body->data[0]->site_survey_date, 'America/Los_Angeles');
                    $startTime->setTimezone('UTC');
//                    dump($body->data);
                    $appointment = new  Appointment();
                    $appointment->lead_id = $lead->id;
                    $appointment->type_id = 4;
                    $appointment->user_id = 1;
                    $appointment->created_by = 1;
                    $appointment->start_time = $startTime->toDateTimeString() ;
                    $appointment->finish_time = $startTime->addHours(2)->toDateTimeString();
                    $appointment->subject = 'Site Survey @ ' . $lead->customer->full_name;
                    $appointment->save();
                }elseif ($body->data[0]->status !== 'OPEN') {
                    $appointment = new  Appointment();
                    $appointment->lead_id = $lead->id;
                    $appointment->type_id = 4;
                    $appointment->user_id = 1;
                    $appointment->created_by = 1;
                    $appointment->start_time = Carbon::parse($lead->salesPacket->cpuc_doc_signed)->toDateTimeString(); ;
                    $appointment->finish_time = $lead->salesPacket->cpuc_doc_signed->timeZone('America/Los_Angeles')->addHours(2)->toDateTimeString();
                    $appointment->subject = 'Site Survey @ ' . $lead->customer->full_name;
                    $appointment->save();
                }
            }
            $i++;
        }

    }

    function getKey($lead)
    {
        $regionId = $lead->office()->first()->market_id;
        return Cache::remember('regionKey' . $regionId, 60000, function () use ($regionId) {
            return RegionAPI::where('region_id', $regionId)->where('type', 'complete-x-api-key')->first()->api_key;
        });
    }
}
