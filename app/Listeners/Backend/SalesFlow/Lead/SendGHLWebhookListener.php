<?php

namespace App\Listeners\Backend\SalesFlow\Lead;

use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\Epc\SolarModule;
use App\Models\Office\Market\RegionAPI;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SendGHLWebhookListener  implements ShouldQueue
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
        if ($event->lead->customer->state !== 'CA') {
            return true;
        }



        $payload = [
            'source' => 'Scout',
            'scout_id' => $event->lead->id,
            'language' => ucfirst($event->lead->customer->language),
            'address' => [
                'street' => $event->lead->customer->street_address,
                'city' => $event->lead->customer->city,
                'state' => 'CA',
                'zip' => $event->lead->customer->zip_code,
            ],

            'customer' => [
                'first_name' => $event->lead->customer->first_name,
                'last_name' => $event->lead->customer->last_name,
                'email' => $event->lead->customer->email,
                'phone' => $event->lead->customer->cell_phone
            ],


        ];

        $client = new Client([

            'timeout' => 500.0,
        ]);
        //env('COMPLETE_API')
        try {
            $response = $client->request('POST', 'https://services.leadconnectorhq.com/hooks/oCTaXfceTQMi30Zp5HNr/webhook-trigger/1c0ba446-f1cd-4803-bb9d-2f1576aa34de', [

                'json' => $payload
            ]);
            $body = json_decode($response->getBody()->getContents());
            Log::debug('response from complete: ' . json_encode([$body]));
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            Log::alert('Error response from GHL:  ' . $responseBodyAsString);

        }




    }


}
