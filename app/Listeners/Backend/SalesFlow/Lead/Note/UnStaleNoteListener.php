<?php

namespace App\Listeners\Backend\SalesFlow\Lead\Note;

use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnStaleNoteListener implements ShouldQueue
{

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
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $lead = Lead::where('id', $event->leadId)->first();
        if ($lead->stale){
            $lead->update(['stale' => null]);
        }
    }
}
