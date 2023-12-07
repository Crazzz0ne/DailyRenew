<?php


namespace App\Events\Backend\SalesFlow\Queue;


use App\Http\Resources\Lead\LineResource;
use App\Models\SalesFlow\Lead\Line;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueTakenEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $queue;

    public function __construct($queue)
    {

        $this->queue = $queue;
        $this->dontBroadcastToCurrentUser();

    }

    public function broadcastOn()
    {
        return new PrivateChannel('Queue');
    }
}
