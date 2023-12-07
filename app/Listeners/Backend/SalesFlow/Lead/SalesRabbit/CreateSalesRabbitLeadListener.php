<?php


namespace App\Listeners\Backend\SalesFlow\Lead\SalesRabbit;


use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\Auth\UserApi;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CreateSalesRabbitLeadListener implements ShouldQueue
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

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {

        die();
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.salesrabbit.com/',
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);

        $noLead = false;

        $salesRabbitUserKey = UserApi::where('user_id', $event->user->id)
            ->where('type','sales-rabbit')
            ->first();

        if(!$salesRabbitUserKey)
            return null;

        $payload = [
            'owners' => $salesRabbitUserKey->api_key ?? null,
            'address[PostalCode]' => $event->lead->customer->zip_code
        ];

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://api.salesrabbit.com/',
            // You can set any number of default request options.
            'timeout' => 500.0,
        ]);

        try {
            $response = $client->request('GET', '/leads?owners=911705284&address[PostalCode]='.$event->lead->customer->zip_code, [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('sales_rabbit_api'),
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);
            $body = json_decode($response->getBody());
            Log::debug('response from SalesRabbit: ' . json_encode([$body]));

        } catch (ClientException $e) {
            $noLead = true;
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::alert('Error response from SalesRabbit:  ' . $responseBodyAsString);
        }
        $SalesRabbitLeadId = null;



        if (!$noLead) {
            foreach ($body->data as $salesRabbitLead) {
                if ($event->lead->customer->street_address == $salesRabbitLead->street1) {
                    $SalesRabbitLeadId = $salesRabbitLead->id;
                    break;
                }
            }
        }

        $payload = [
            'data' => [
                'userId' => $salesRabbitUserKey->api_key ?? null,
                'status' => 'Transfer',
            ]
        ];

        if ($SalesRabbitLeadId) {
            try{
            $response = $client->request('PUT', '/leads/'.$SalesRabbitLeadId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('sales_rabbit_api'),
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);
                $responseBodyAsString = $response->getBody()->getContents();
                Log::debug('Update Lead Payload to from SalesRabbit:  ' , $payload);
                Log::debug('Update Lead response from SalesRabbit:  ' . $responseBodyAsString);
            } catch (ClientException $e) {

                $responseBodyAsString =  $e->getResponse()->getBody(true);

                Log::alert('Error response update Lead from SalesRabbit:  ' . $responseBodyAsString);
//            TwilioSMSController::sendSms('6199406423', 'complete lead create failed lead id ' . $event->lead->id);
            }

        } else {
            $payload = [
                'data' => [
                    'userId' => $salesRabbitUserKey->api_key ?? null,
                    'status' => 'Transfer',
                    'street1' => $event->lead->customer->street_address,
                    'city' => $event->lead->customer->city,
                    'zip' => $event->lead->customer->zip_code,
                    'state' => $event->lead->customer->state
                ]
            ];
            try {
                $response = $client->request('POST', '/leads', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . env('sales_rabbit_api'),
                        'Content-Type' => 'application/json'
                    ],
                    'json' => $payload
                ]);
                $responseBodyAsString = $response->getBody()->getContents();
                Log::debug('Create Lead response from SalesRabbit:  ' . $responseBodyAsString);
            } catch (ClientException $e) {

                $responseBodyAsString =  $e->getResponse()->getBody(true);
                Log::alert('Error response Creat Lead from SalesRabbit:  ' . $responseBodyAsString);
//            TwilioSMSController::sendSms('6199406423', 'complete lead create failed lead id ' . $event->lead->id);
            }
        }

        $body = json_decode($response->getBody());
        return $body;

    }
}
