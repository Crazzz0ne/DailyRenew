<?php

namespace App\Events\Backend\SalesFlow\Customer;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerAppointmentEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $customerPhone, $customerId, $repId, $appointmentTime, $user;

    public function __construct($customerPhone, $customerId, $repId, $appointmentTime, $user)
    {
        $this->appointmentTime = $appointmentTime;
        $this->repId = $repId;
        $this->customerPhone = $customerPhone;
        $this->customerId = $customerId;
        $this->user = $user;

//        $this->dontBroadcastToCurrentUser();
    }


}
