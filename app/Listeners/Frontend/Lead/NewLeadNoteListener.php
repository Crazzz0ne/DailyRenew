<?php

namespace App\Listeners\Frontend\Lead;

use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Models\Auth\User;
use App\Models\Notifications\NotificationBatch;
use App\Models\SalesFlow\Lead\Lead;
use App\Notifications\Frontend\Lead\NewLeadNoteNotification;
use App\Notifications\Frontend\Lead\NewLeadNoteUserNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NewLeadNoteListener
{
    use InteractsWithSockets;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param LeadNewMessageEvent $event
     * @return void
     */
    public function handle($event)
    {

        //Make new notification batch. This will be how we later identify all related notifications.
        $batch = new NotificationBatch();
        $batch->save();

        $lead = Lead::find($event->leadId);
        $users = $lead->reps->unique();
        $tempArray = [];
        foreach ($users as $user) {
            if ($user->terminated == null) {
                if ($event->lead->close_date !== null){
                    if ($user->hasRole('sp2') || $user->hasRole('manager') || $user->hasRole('account manager')
                        || $user->hasRole('regional manager') || $user->hasRole('roof assessor') || $user->hasRole('sales manager')){
                        $tempArray[] = $user;
                    }
                } else {
                    $tempArray[] = $user;
                }
            }

        }

        $users = $tempArray;

        if ($lead->close_date) {
            $userArray = [];
            foreach ($users as $user) {
                if ($user->pivot->position_id === 9 || $user->pivot->position_id === 3 && $user->terminated == null) {
                    array_push($userArray, $user->id);
                }
            }
            $users = User::whereIn('id', $userArray)->get();

        }


        Notification::send($users, new NewLeadNoteUserNotification($event, $batch->id));

    }
}
