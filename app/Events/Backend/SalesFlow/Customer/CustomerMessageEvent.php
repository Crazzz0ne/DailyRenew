<?php

namespace App\Events\Backend\SalesFlow\Customer;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message, $customerId;

    public function __construct($message,  $customerId)
    {
        $this->customerId = $customerId;

        $this->message = $message;

        $this->dontBroadcastToCurrentUser();

    }

    public function broadcastOn()
    {
        return new PrivateChannel('customer.' . $this->customerId);
    }

}
