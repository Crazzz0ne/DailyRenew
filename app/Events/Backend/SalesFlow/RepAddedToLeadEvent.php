<?php


namespace App\Events\Backend\SalesFlow;


use App\Http\Resources\LeadRepsResource;
use App\Http\Resources\RepresentativeResource;
use App\Http\Resources\UserResource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Position\Position;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class RepAddedToLeadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leadId;
    public $rep;


    public function __construct($rep, $leadId, $shouldBroadcastToSelf = false)
    {


        $this->leadId = $leadId;
        $this->rep = $rep;
        if (!$shouldBroadcastToSelf) {
            $this->dontBroadcastToCurrentUser();
        }
    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->leadId);
    }
}
