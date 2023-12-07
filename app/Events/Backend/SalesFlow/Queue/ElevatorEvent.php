<?php


namespace App\Events\Backend\SalesFlow\Queue;




use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class ElevatorEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $elevator;
    public $type;

    public function __construct($type, $elevator)
    {
        $this->type = $type;
        $this->elevator = $elevator;
        $this->dontBroadcastToCurrentUser();

    }
    public function broadcastOn()
    {
        return new PrivateChannel('Queue');
    }
}
