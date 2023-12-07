<?php

namespace App\Notifications;

use App\Channels\TwilioChannel;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewEmployeeRegisteredNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, InteractsWithSockets;

    protected $event;
    protected $url;
    protected $body;
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event, $user)
    {
        Log::info("Creating New Employee Registered Notification!" . json_encode($event));
        $this->event = $event;
        $this->url = "/dashboard/user/{$this->event->user->id}";
        $this->body = "New Employee Registered. {$event->user->id}";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail', 'database', 'broadcast', TwilioChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject("New Employee Registered.")
                    ->action('Notification Action', url($this->url))
                    ->line($this->body);
    }

    /**
     * Return an array specially formatted to work with Twilio
     * @param $notifiable
     * @return array
     */
    public function toTwilio($notifiable): array
    {
        return [
            'body' => $this->body . "\n\n" . url($this->url),
            'from' => env('TWILIO_PHONE_NUMBER')
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //App Notification Dependencies
            'notification_id' => $this->id,
            'batch_id' => null,
            'url' => $this->url,
            'body' => $this->body,
            'time' => Carbon::now(),
            'read_at' => null,
            'img' => $this->event->triggeringUser->gravatar ?? null,
        ];
    }

    public function broadcastAs(): string
    {
        return "NewEmployeeRegistered";
    }
}
