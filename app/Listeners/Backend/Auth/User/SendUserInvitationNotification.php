<?php


namespace App\Listeners\Backend\Auth\User;


use App\Mail\Announcement\AnnouncementMailable;
use App\Mail\SalesFlow\BaseMailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendUserInvitationNotification
{




    public function handle($invitation)
    {

        Mail::to($invitation->invitation->email)->queue(new BaseMailable("You have been invited by ". $invitation->invitation->user->name, 'Click the link to register',
            URL::to('/register/'.$invitation->invitation->code), 'sell solar!'));

    }

}
