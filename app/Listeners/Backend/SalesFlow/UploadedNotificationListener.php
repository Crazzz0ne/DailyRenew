<?php


namespace App\Listeners\Backend\SalesFlow;


use App\Mail\Announcement\AnnouncementMailable;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class UploadedNotificationListener
{




    public function handle($event)
    {

        $lead = Lead::where('id', $event->leadId)->with('reps', 'customer')->first();


        foreach ($lead->reps as $rep){
            if ($rep->id !== $event->data->user_id && $rep->pivot->position_id === 6 && $event->data->type === 'bill' ){
                Mail::to($rep->email)->queue(new BaseMailable( 'Bill upload('.$lead->id.')',
                    'New Upload', URL::to('/dashboard/lead/'.$lead->id), 'Lead'));
            }
        }


    }

}
