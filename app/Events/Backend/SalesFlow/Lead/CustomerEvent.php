<?php


namespace App\Events\Backend\SalesFlow\Lead;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerEvent  implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data, $leadId, $id;

    public function __construct($data, $id, $leadId)
    {
        $this->leadId = $leadId;
        $this->id = $id;
        $this->data = $data;
        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.'.$this->leadId );
    }
}
