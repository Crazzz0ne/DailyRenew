<?php


namespace App\Handler\Webhook;



use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Spatie\WebhookClient\ProcessWebhookJob;

class TwilioHandler extends ProcessWebhookJob
{

    public function handle($message)
    {
        return $message;
//        dd($message);
////        $payload = $this->webhookCall->payload;
//        Log::info("Twilio" ,[$request]);
//
////        $body = $payload['Body'];



    }

}
