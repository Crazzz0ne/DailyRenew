<?php

namespace App\Listeners\Backend\SalesFlow\Lead\Appointment;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnstaleAppointmentListener implements ShouldQueue
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
        $lead = Lead::where('id', $event->appointment->lead_id)->first();
        if ($lead->stale) {
            $lead->update(['stale' => null]);
        }
    }
}
