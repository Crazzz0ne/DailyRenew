<?php

namespace App\Events\Backend\SalesFlow\Customer;
use App\Models\SalesFlow\Customer\CustomerMessages;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerMessageStatusUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CustomerMessages $customerMessage;

    public function __construct(CustomerMessages $customerMessage)
    {
        $this->customerMessage = $customerMessage;
    }

    public function broadcastOn()
    {
        \Log::info('CustomerMessageStatusUpdatedEvent', [ $this->customerMessage]);
        // You can define a channel specific to the customer or use a general channel
        return new PrivateChannel('customer.' . $this->customerMessage->customer_id);
    }
}
