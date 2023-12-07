<?php


namespace App\Repositories\Backend\Notifications\NotificationRepository;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use League\Flysystem\Exception;

class NotificationRepository extends BaseRepository
{
    public function model()
    {
        return Appointment::class;

    }





}
