<?php

namespace App\Handler\Webhook;

use App\Events\Backend\SalesFlow\Customer\CustomerMessageStatusUpdatedEvent;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Customer\CustomerMessages;
use Log;
use Spatie\WebhookClient\ProcessWebhookJob;

class TwilioResponseHandler extends ProcessWebhookJob
{
    /**
     * @throws \Exception
     */
    public function handle()
    {

        $payload = $this->webhookCall;
        $fromNumber = $payload->payload['From'];
        $toNumber = $payload->payload['To'];
        $messageSid = $payload->payload['MessageSid']; // Obtain the message SID
        $status = $payload->payload['SmsStatus']; // Get the status

        $statusHierarchy = [
            null =>0,
            'queued' => 1,
            'sending' => 2,
            'sent' => 3,
            'undelivered' => 4, // Twilio will retry delivery for 72 hours
            'delivered' => 5,
        ];

        $customer = Customer::where('cell_phone', substr($toNumber, 2))
            ->orWhere('home_phone', substr($toNumber, 2))
            ->first();
        if (!$customer) {
            Log::error('Customer not found for number: ' . $toNumber);
//            Log whole payload
            Log::error('le payload', [$payload]);
//            throw error
            throw new \Exception("Webhook Error: Customer not found for number: " . $toNumber);
            return;
        }

        $customer->twilio_number = $fromNumber;
        $customer->save();

        $customerMessage = CustomerMessages::where('message_sid', $messageSid)->first();
        if ($customerMessage) {
            // Check if the incoming status is later in the sequence than the current status in the database.
            Log::debug('Customer Message Status: ' . $customerMessage->status . ' Incoming Status: ' . $status);
            if (!$customerMessage->status){
                $customerMessage->status = $status;
                $customerMessage->save();
                Log::debug('Type of $customerMessage', [gettype($customerMessage), get_class($customerMessage)]);
                event(new CustomerMessageStatusUpdatedEvent($customerMessage));
                return 1;
            }
            if ($statusHierarchy[$status] > $statusHierarchy[$customerMessage->status]) {
                $customerMessage->status = $status;
                $customerMessage->save();
                Log::debug('Type of $customerMessage', [gettype($customerMessage), get_class($customerMessage)]);
                event(new CustomerMessageStatusUpdatedEvent($customerMessage));
            }
        }

    }


}
