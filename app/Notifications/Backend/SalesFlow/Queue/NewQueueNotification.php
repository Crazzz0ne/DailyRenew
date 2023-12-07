<?php

namespace App\Notifications\Backend\SalesFlow\Queue;

use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NewQueueNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, InteractsWithSockets;
    public $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(NewQueueEvent $event)
    {
        $this->event = $event;
        $this->__wakeup();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
            return ['broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
        public function toArray($notifiable): array
    {
        //Has No Notification Display Dependencies
        return [
            'location' => $this->event->location,
            'payload' => $this->event->payload,
            'direction' => $this->event->direction,
            'message' => $this->event->message,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('Queue');
    }
}
