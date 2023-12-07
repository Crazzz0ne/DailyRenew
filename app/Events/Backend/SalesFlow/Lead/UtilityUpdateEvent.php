<?php


namespace App\Events\Backend\SalesFlow\Lead;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UtilityUpdateEvent  implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data, $leadId;

    public function __construct( $data, $leadId)
    {

        $this->leadId = $leadId;

        $this->data = $data;
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->leadId);
    }
}

