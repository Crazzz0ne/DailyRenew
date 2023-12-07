<?php


namespace App\Listeners\Backend\SalesFlow;


use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Newsletter\NewsletterFacade as Newsletter;

class MailChimpDripAddListener implements ShouldQueue
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
        $customerInfo = ['FNAME' => $event->firstName, 'LNAME' => $event->lastName];
        $added = Newsletter::subscribeOrUpdate($event->email, ['FNAME' => $event->firstName, 'LNAME' => $event->lastName], '');
        if ($added) {
            Newsletter::addTags(['installed'], $event->email, '');
            $update = MassText::where('email', '=', $event->email)->first();
            $update->sent = true;
            $update->sent_date = Carbon::now();
            $update->save();
        }
    }


}
