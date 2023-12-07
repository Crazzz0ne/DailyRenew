<?php


namespace App\Events\Backend\SalesFlow\Queue;


use App\Models\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewQueueEvent
{
    use Dispatchable, SerializesModels;

    public $payload;
    public $direction;
    public $location;
    public $triggeringUser;
    public $message;

    public function __construct($payload, $direction, $message, $location = null, User $user = null)
    {
        $this->location = $location;
        $this->payload = $payload;
        $this->message = $message;
        $this->direction = $direction;
        $this->triggeringUser = $user;
    }
}
