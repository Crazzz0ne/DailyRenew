<?php

namespace App\Channels;

use App\Notifications\Backend\SalesFlow\Queue\NewUserQueueNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\RestException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     * @return void
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function send($notifiable, Notification $notification)
    {
        $account_sid = config('services.twilio.account_sid');
        $password = config('services.twilio.password');
        $phone_number = $notifiable->phone_number ?? null;

        if (!$phone_number){
            Log::debug('No phone number found for user: ' . $notifiable->id);
        }

        if ($account_sid && $password && $phone_number) {
            $options = $notification->toTwilio($notifiable);

            try {
                $client = new Client($account_sid, $password);
                $message = $client->messages->create($phone_number, $options);
            } catch (\Exception $e) {
                Log::warning($e);
            }
        }
    }
}
