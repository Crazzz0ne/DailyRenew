<?php

namespace App\Notifications\Frontend\Lead;

use App\Channels\TwilioChannel;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class NewLeadNoteNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, InteractsWithSockets;

    public $event;



    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LeadNewMessageEvent $event)
    {
//        Log::info('New Lead Message Notification Constructing...');
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
//        Log::info('Sending To: ', [$notifiable]);
        if ($notifiable->id !== $this->event->payload['user']['id'])
            //TODO: Take User profile settings into account.
            //TODO: Maybe only make a notification if message is marked as urgent?
            return ['broadcast'];
        else return []; //If triggeringUser is the same notifiable we're currently sending to, just don't send anything anywhere.
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->line($this->body)
            ->action('View Conversation', url($this->url));
    }

    /**
     * Return an array specially formatted to work with Twilio
     * @param $notifiable
     * @return array
     */


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->event->message,
            'leadId' => $this->event->leadId,
            'firstName' => $this->event->firstName,
            'lastName' => $this->event->lastName,
            'id' => $this->event->id,
            'gravatar' => $this->event->gravatar,
            'payload' => $this->event->payload,
        ];
    }
}
