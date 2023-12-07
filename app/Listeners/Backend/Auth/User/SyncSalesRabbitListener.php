<?php

namespace App\Listeners\Backend\Auth\User;

use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\Auth\User;
use App\Models\Auth\UserApi;
use App\Models\Epc\SolarModule;
use App\Models\Office\Market\Market;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SyncSalesRabbitListener implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    public function handle()
    {

        $markets = Market::whereHas('apiKey', function ($q) {
            $q->where('type', 'sales-rabbit');
        })->with('apiKey')->get();
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.salesrabbit.com',
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);
        foreach ($markets as $market) {
            $apiKey = $market->apiKey['api_key'];

            try {
                $response = $client->request('GET', '/users', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey
                    ],
                ]);
                $body = json_decode($response->getBody()->getContents());


//                Log::debug('response from complete: ' . json_encode([$body]));
//                Log::debug('Lead updated from close: ' . json_encode([$lead]));
            } catch (ClientException | \Exception $e) {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
//            Log::info('response from complete: ' . json_encode([$body]));
                Log::alert('Error response from complete:  ' . $responseBodyAsString);

            }
            $users = collect($body);

            foreach ($users['data'] as $userAPI) {

                $user = User::where('email', strtolower($userAPI->email))->first();
                if ($user) {
                    UserApi::updateOrCreate(
                        [
                            'type' => 'sales-rabbit',
                            'user_id' => $user->id,
                        ],
                        ['api_key' => $userAPI->id]
                    );
                }

            }
        }
    }
}
