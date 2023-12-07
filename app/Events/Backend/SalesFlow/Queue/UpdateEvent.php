<?php


namespace App\Events\Backend\SalesFlow\Queue;


use App\Http\Resources\Lead\LineResource;
use App\Models\SalesFlow\Lead\Line;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $queueId;
    public $relatedQueue;

    public function __construct($queue)
    {


        $payload = Line::where('id', '=', $queue->id)
            ->with('requestingUser', 'filledUser')
            ->get()
            ->first();


        $this->data = new LineResource($payload);
        $this->queueId = $queue->id;
        $queues = Line::where('id', '!=', $payload->id)
            ->where('lead_id', $payload->lead_id)->get();
        $this->relatedQueue = LineResource::collection($queues);

        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('Queue.' . $this->queueId);
    }
}
