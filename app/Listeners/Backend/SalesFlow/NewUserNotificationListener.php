<?php

namespace App\Listeners\Backend\SalesFlow;

use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Models\Notifications\NotificationBatch;
use App\Notifications\Backend\SalesFlow\Queue\NewUserQueueNotification;
use App\Repositories\Backend\Auth\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NewUserNotificationListener
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
     * @param NewUserNotificationEvent $event
     * @return void
     */
    public function handle(NewUserNotificationEvent $event)
    {
        //Make new notification batch. This will be how we later identify all related notifications.
        $batch = new NotificationBatch();
        $batch->save();

        $users = $this->userRepository->userByPosition($event->payload->type, $event->payload->lead->office_id);


        Notification::send($users, new NewUserQueueNotification($event, $batch->id));
    }
}
