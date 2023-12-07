<?php

namespace App\Notifications\Backend\SalesFlow\Queue;

use App\Channels\TwilioChannel;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NewUserQueueNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, InteractsWithSockets;

    public $event;
    public $batch_id;
    protected $url = '/dashboard/lead/queue';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(NewUserNotificationEvent $event, $batch_id)
    {
        $this->event = $event;
        $this->batch_id = $batch_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        switch ($this->event->payload['type']) {
            case 'integrations':
                return ['database', 'broadcast', TwilioChannel::class];
            case 'build_proposal':
                if ($this->event->payload['urgent']) {
                    return ['mail', 'database', 'broadcast', TwilioChannel::class];
                } else {
                    return ['mail', 'database', 'broadcast'];
                }
            case 'sp1':
                return ['mail', 'database', 'broadcast', TwilioChannel::class];
            case 'credit_app':
                return ['mail', 'database', 'broadcast', TwilioChannel::class];
            case 'sun_run_runner':
                return ['mail', 'database', 'broadcast', TwilioChannel::class];
            case'd2d_call_center':
                return ['mail', 'database', 'broadcast', TwilioChannel::class];
            case'change_order':
                return ['mail', 'database', 'broadcast', TwilioChannel::class];
            case'roof':
                return ['mail', 'database', 'broadcast', TwilioChannel::class];
            default:
                return ['mail', 'database', 'broadcast', TwilioChannel::class];
        }
        //TODO: Take User profile settings into account.
//        if ('integrations' === $this->event->payload['type']) {
//            return ['mail', 'database', 'broadcast', TwilioChannel::class];
//        }else{
//            return ['mail', 'database', 'broadcast', TwilioChannel::class];
//        }
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->convertRequestTypeSubjectToString())
            ->line($this->convertRequestTypeBodyToString())
            ->action('Accept', url($this->url));
    }

    /**
     * Return an array specially formatted to work with Twilio
     *
     * @param $notifiable
     * @return array
     */
    public function toTwilio($notifiable): array
    {
        return [
            'body' => $this->convertRequestTypeBodyToString() . "\n\n" . url($this->url),
            'from' => env('TWILIO_PHONE_NUMBER')
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //App Notification Dependencies
            'notification_id' => $this->id,
            'batch_id' => $this->batch_id,
            'url' => $this->url,
            'body' => $this->convertRequestTypeBodyToString(),
            'time' => Carbon::now(),
            'read_at' => null,
            'img' => $this->event->triggeringUser->gravatar ?? null,

            //Extra Dependencies
            'location' => $this->event->location,
            'payload' => $this->event->payload,
            'direction' => $this->event->direction,
        ];
    }

    public function convertRequestTypeBodyToString(): string
    {
        $type = $this->event->payload->type;

        switch ($type) {
            case $type === 'sp1':
            {
                return "New Lead! SP1\n{$this->event->payload->lead->customer->full_name}\nIf you do not wish to accept this opportunity, you may ignore this notification.";
            }
            case $type === 'build_proposal':
            {
                if ($this->event->payload->urget)
                    return "URGENT Proposal \n{$this->event->payload->lead->customer->full_name}\nIf you do not wish to accept this opportunity, you may ignore this notification.";
                return "Can you please create a proposal? \n{$this->event->payload->lead->customer->full_name}\nIf you do not wish to accept this opportunity, you may ignore this notification.";
            }
            case $type === 'integrations':
            {

                return "Can you please create an account and run integrations? \n{$this->event->payload->lead->customer->full_name}";
            }
            case $type === 'credit_app':
            {
                return "Can you please create an account and run Credit for \n{$this->event->payload->lead->customer->full_name}";
            }
            case $type === 'sun_run_runner':
            {
                return "Can you please send documents to sign for \n{$this->event->payload->lead->customer->full_name}?";
            }
            case $type === 'd2d_call_center':
            {
                return "Can you call {$this->event->payload->lead->customer->full_name}, they are ready to start saving!";
            }
            case $type === 'roof':
            {
                return "Can you take a look at the roof for {$this->event->payload->lead->customer->full_name}, they are ready to start saving!";
            }
            case $type === 'change_order':
            {
                return "Can you create a change order for {$this->event->payload->lead->customer->full_name}, they are ready to start saving!";
            }
            default:
            {
                return "New Item In The Queue";
            }
        }
    }

    public function convertRequestTypeSubjectToString(): string
    {
        $type = $this->event->payload->type;

        switch ($type) {
            case $type === 'sp1':
            {
                return "new sp1 request";
            }
            case $type === 'build_proposal':
            {
                if ($this->event->payload->urgent) {
                    return 'Urgent Create a proposal (' . $this->event->payload->lead->id . ')';
                }
                return 'Create a proposal(' . $this->event->payload->lead->id . ')';
            }
            case $type === 'integrations':
            {
                return 'Can you please do an Integration(' . $this->event->payload->lead->id . ')';
            }
            case $type === 'credit_app':
            {
                return 'Create a Credit App (' . $this->event->payload->lead->id . ')';
            }
            case $type === 'sun_run_runner':
            {
                return "Can you please send documents to sign? \n{$this->event->payload->lead->customer->full_name}";
            }
            case $type === 'd2d_call_center':
            {
                return "Can you call {$this->event->payload->lead->customer->full_name}?";
            }
            case $type === 'roof':
            {
                return "Roof assessment? \n{$this->event->payload->lead->customer->full_name}";
            }
            case $type === 'change_order':
            {
                return "Can you make a change order {$this->event->payload->lead->customer->full_name}?";
            }
            default:
            {
                return "New Item In The Queue";
            }
        }
    }
}
