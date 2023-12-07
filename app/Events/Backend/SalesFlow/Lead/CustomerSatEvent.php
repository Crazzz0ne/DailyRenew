<?php


namespace App\Events\Backend\SalesFlow\Lead;


use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerSatEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;
    public User $user;

    public function __construct($lead, $user = null)
    {
        $this->lead = $lead;
        $this->user = $user;
    }

}
