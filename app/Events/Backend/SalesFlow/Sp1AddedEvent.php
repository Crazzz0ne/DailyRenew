<?php


namespace App\Events\Backend\SalesFlow;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class Sp1AddedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userName, $phoneNumber, $leadId, $message;

    public function __construct($leadId, $userName, $phoneNumber, $message)
    {
        $this->userName = $userName;
        $this->phoneNumber = $phoneNumber;
        $this->leadId = $leadId;
        $this->message = $message;

    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->leadId);
    }

}
