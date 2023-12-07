<?php


namespace App\Events\Backend\SalesFlow\Lead\SalesRabbit;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateSalesRabbitLeadEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public  $lead, $user;

    public function __construct($lead, $user)
    {
       $this->user = $user;
        $this->lead = $lead;
    }
}
