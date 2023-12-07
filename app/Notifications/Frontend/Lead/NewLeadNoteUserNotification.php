<?php

namespace App\Notifications\Frontend\Lead;

use App\Channels\TwilioChannel;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class NewLeadNoteUserNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, InteractsWithSockets;

    public $event;
    public $batch_id;
    protected $subject;
    protected $body;
    protected $url = '';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LeadNewMessageEvent $event, $batch_id)
    {
        $urgent = '';
        if ($event->urgent) {
            $urgent = '🔴 ';
        }
        if ($event->payload->user_id === 1) {
            $subject = $urgent . 'Message from Scout' .
                ' on lead  (' . $event->lead->customer->first_name . " " . $event->lead->customer->last_name . ')';
        } else {
            $subject = $urgent . 'Message from ' .
                $event->firstName . " " . $event->lastName .
                ' on lead  (' . $event->lead->customer->first_name . " " . $event->lead->customer->last_name . ')';
        }

        $this->event = $event;
        $this->event->__wakeup();
        $this->batch_id = $batch_id;
        $this->url = URL::to('/') . '/dashboard/lead/' . $event->leadId;

        $this->subject = $subject;


        $this->body = 'For customer ' . $this->event->lead->customer->first_name . ' ' . $this->event->lead->customer->last_name . ':  ' . $event->message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {

        if ($notifiable->id !== $this->event->payload['user']['id']) {
            //TODO: Take User profile settings into account.
            //TODO: Maybe only make a notification if message is marked as urgent?
            if ($this->event->urgent) {

                return ['mail', 'database', 'broadcast', TwilioChannel::class];
            } else {
                return ['database', 'broadcast'];
            }
        } else {
            return [];
        } //If triggeringUser is the same notifiable we're currently sending to, just don't send anything anywhere.
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
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
    public function toTwilio($notifiable): array
    {
        return [
            'body' => $this->body . "\n\n" . url($this->url) . "\n\n Do not reply. If you wish to continue the conversation, please follow the link provided in this message.",
            'from' => env('TWILIO_PHONE_NUMBER')
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //App Notification Dependencies
            'notification_id' => $this->id,
            'batch_id' => $this->batch_id,
            'url' => $this->url,
            'body' => $this->body,
            'img' => $this->event->gravatar ?? null,
            'read_at' => null,
            'time' => Carbon::now(),

            //Extra Dependencies
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
