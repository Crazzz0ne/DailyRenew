<?php

namespace App\Listeners\Backend\SalesFlow;

use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Notifications\Backend\SalesFlow\Queue\NewQueueNotification;
use App\Repositories\Backend\Auth\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NewQueueListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    protected $userRepository;

    public function __construct()
    {
        // set the model
        $this->userRepository = new UserRepository();
    }

    /**
     * Handle the event.
     *
     * @param  NewQueueEvent  $event
     * @return void
     */
    public function handle(NewQueueEvent $event)
    {
        $type = $event->payload->type;

        $users = $this->userRepository->userByPosition($event->payload->type, $event->payload->lead->office_id);
        Notification::send($users, new NewQueueNotification($event));
    }
}
