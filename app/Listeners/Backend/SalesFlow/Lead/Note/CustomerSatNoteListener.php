<?php

namespace App\Listeners\Backend\SalesFlow\Lead\Note;

use App\Events\Backend\SalesFlow\Lead\Note\NewNoteEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Models\SalesFlow\Lead\LeadNote;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerSatNoteListener  implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {

        $note = new LeadNote();
        $note->lead_id = $event->lead->id;
        $note->user_id = $event->user->id;
        $note->note = 'Hurray the customer Sat!';
        $note->save();

        broadcast(new LeadNewMessageEvent($event->lead->id, $note->id, $note->note, $event->user->id, 0));
        event(new NewNoteEvent($event->lead->id, $note->id, $note->note, $event->user->id, 0));


    }
}
