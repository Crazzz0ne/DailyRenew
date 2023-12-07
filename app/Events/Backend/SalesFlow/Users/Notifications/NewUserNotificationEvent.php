<?php


namespace App\Events\Backend\SalesFlow\Users\Notifications;


use App\Models\Auth\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewUserNotificationEvent
{
    use Dispatchable, SerializesModels;

    public $payload;
    public $direction;
    public $location;



    /**
     * NewQueueEvent constructor.
     * @param $payload
     * @param User|null $user
     */
    public function __construct($payload, $location = null, $direction = null)
    {
        $this->payload = $payload;

    }
}
