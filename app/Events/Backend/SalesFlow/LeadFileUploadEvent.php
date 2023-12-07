<?php


namespace App\Events\Backend\SalesFlow;



use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class LeadFileUploadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $leadId, $data;

    public function __construct($leadId, $data)
    {

        $this->leadId = $leadId;
        $this->data = $data;

//        $this->dontBroadcastToCurrentUser();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->leadId);
    }


}
