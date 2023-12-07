<?php


namespace App\Events\Backend\SalesFlow\Queue;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class QueuePageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payload;
    public $direction;
    public $location;


    public function __construct($payload, $direction, $location = null)
    {
        $this->location = $location;
        $this->payload = $payload;
        $this->direction = $direction;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('Queue');
    }
}
