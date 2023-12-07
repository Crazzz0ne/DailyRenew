<?php

namespace App\Notifications;

use App\Channels\TwilioChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwilioWelcomeNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $body = "Hello from Scout, Reply START to receive future messages! Reply QUIT to unsubscribe";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TwilioChannel::class];
    }

    public function toTwilio($notifiable)
    {
        return [
            'body' => $this->body,
            'from' => env('TWILIO_PHONE_NUMBER')
        ];
    }
}
