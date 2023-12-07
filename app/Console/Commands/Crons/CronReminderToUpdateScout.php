<?php


namespace App\Console\Commands\Crons;


use App\Events\Backend\SalesFlow\LeadEditEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Http\Controllers\HelperController;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Log;
use Illuminate\Support\Facades\URL;

class CronReminderToUpdateScout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cronAppointmentFollowUp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminds reps of their appointment';

    protected $leadRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *

     */
    public function handle()
    {
//
        $appointments = Appointment::where('finish_time',
            Carbon::now()->addMinutes(15)->setSeconds(0)->toDateTimeString())
            ->where('type_id', 6)
            ->get();

        foreach ($appointments as $appointment) {
            $user = User::where('id', $appointment->user_id)->first();

            if (!$appointment->lead->customer){
                break;
            }
            TwilioSMSController::sendSms($user->phone_number, 'Have you updated Your lead? ' . $appointment->lead->customer->last_name . '
            Pass credit?
            Close?
            Some Notes?
            Customer Sat?
            Follow up Appointment?
            ' . URL::to('/') . '/dashboard/lead/' . $appointment->lead->id);

            HelperController::email("Have you updated Your lead?.". $appointment->lead->customer->last_name,
            "Pass credit?
            Close?
            Some Notes?
            Customer Sat?
            Follow up Appointment?" ,
                URL::to('/') . '/dashboard/lead/' . $appointment->lead->id, $user, 'Lead');

        }


    }
}
