<?php

namespace App\Notifications\Frontend\Auth;

use App\Channels\TwilioChannel;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserWelcomeNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, InteractsWithSockets;

    protected $event;
    protected $url;
    protected $body = "Welcome to scout, we're happy to have you aboard!
    Feel free to take a look around and familiarize yourself with the application.";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
        $this->url = "/dashboard";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        //TODO: Send Text message too?
        return ['mail', 'database', 'broadcast'];
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
                    ->subject("Welcome To Scout!")
                    ->line("We're glad to have you aboard {$notifiable->first_name} {$notifiable->last_name}.")
                    ->action('Dashboard', url('/dashboard'))
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
}
