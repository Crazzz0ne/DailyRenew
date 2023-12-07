<?php


namespace App\Events\Backend\SalesFlow;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalesPacketEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $salesPacket, $leadId;

    public function __construct($salesPacket, $leadId)
    {
        $this->salesPacket = $salesPacket;
        $this->leadId = $leadId;
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->leadId);
    }


}
