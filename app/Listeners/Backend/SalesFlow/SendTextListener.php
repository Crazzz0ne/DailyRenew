<?php

namespace App\Listeners\Backend\SalesFlow;

use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTextListener implements ShouldQueue
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


        TwilioSMSController::sendSms($event->too, $event->body);
    }


}
