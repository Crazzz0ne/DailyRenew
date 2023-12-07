<?php


namespace App\Events\Backend\SalesFlow;


use App\Models\Auth\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadEditEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $leadId;
    public $userId;
    public $userName;

    public function __construct($leadID, $message,  $userId, $userName)
    {
        $this->leadId = $leadID;
        $this->message = $message;
        $this->userId = $userId;
        $this->userName = $userName;


        $this->dontBroadcastToCurrentUser();

    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->leadId);
    }
}
