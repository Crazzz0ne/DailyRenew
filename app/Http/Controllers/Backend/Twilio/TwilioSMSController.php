<?php

namespace App\Http\Controllers\Backend\Twilio;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

class TwilioSMSController extends Controller
{
    //TODO: Check if this is needed anymore now that notifiable has been implemented and laravel notifications are now being broadcast instead of events.
    public static function sendSms($too, $body, $twilioNumber = '+18557856590')
    {

        $to = '+1' . $too;

        if ($to === '+1') {
            \Log::alert('someone does not have a number', [$too]);

            return [
                "status" => "400",
                "details" => ['message' => 'no phone number']
            ];
        }

        try {

            $client = new Client('ACc36f06287eb312398fa37684fe9ce3df', '1083573842988cbd1104688cb7c7891a');
            $message = $client->messages->create(
                $to,
                array(
                    'from' => $twilioNumber,
                    'body' => $body,
                )
            );
        } catch (\Twilio\Exceptions\RestException|ConfigurationException $e) {
            \Log::warning($e);
        }
        $data = [
            "status" => "200",
            "details" => ['message' => 'all set']
        ];


        return response()->json($data);
    }
}
