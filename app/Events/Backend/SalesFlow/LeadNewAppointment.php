<?php


namespace App\Events\Backend\SalesFlow;


use App\Models\Auth\User;
use Creativeorange\Gravatar\Facades\Gravatar;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadNewAppointment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;
    public $name;


    public function __construct($appointment, $name)
    {
        $this->appointment = $appointment;
        $this->name = $name;
        $this->dontBroadcastToCurrentUser();

    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->appointment->lead_id);
    }
}
