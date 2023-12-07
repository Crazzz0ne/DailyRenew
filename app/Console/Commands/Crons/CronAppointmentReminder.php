<?php


namespace App\Console\Commands\Crons;


use App\Events\Backend\SalesFlow\LeadEditEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Http\Controllers\HelperController;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Log;
use Illuminate\Support\Facades\URL;

class CronAppointmentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cronAppointmentReminder';

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
        $appointments = Appointment:: where('start_time', Carbon::now()
            ->addMinutes(60)
            ->setSeconds(0)->toDateTimeString())->with('lead.customer')
            ->get();

        foreach ($appointments as $appointment) {
            $user = User::where('id', $appointment->user_id)->first();

            if (!$appointment->lead->customer) {
                break;
            }
            TwilioSMSController::sendSms($user->phone_number, 'You have an appointment with ' . $appointment->lead->customer->first_name . ' ' .
                $appointment->lead->customer->last_name . ' @ ' . $appointment->lead->customer->street_address . ' '
                . $appointment->lead->customer->zip_code . ' in 1 hour  ' . URL::to('/') . '/dashboard/lead/' . $appointment->lead->id);

            HelperController::email('Appointment in One Hour with ' . $appointment->lead->customer->last_name,
                'You have an appointment with ' . $appointment->lead->customer->first_name . ' ' . $appointment->lead->customer->last_name .
                ' @ ' . $appointment->lead->customer->street_address . ' '
                . $appointment->lead->customer->zip_code . ' in 1 hour  ',
                URL::to('/') . '/dashboard/lead/' . $appointment->lead->id, $user, 'Lead');



        }


        $appointments = Appointment:: where('start_time', Carbon::now()->addMinutes(15)->setSeconds(0)->toDateTimeString())
            ->with('lead.customer')
            ->get();

        foreach ($appointments as $appointment) {
            $user = User::where('id', $appointment->user_id)->first();

            if (!$appointment->lead->customer) {
                break;
            }
            TwilioSMSController::sendSms($user->phone_number, 'You have an appointment with ' . $appointment->lead->customer->first_name . ' ' .
                $appointment->lead->customer->last_name . ' @' . $appointment->lead->customer->street_address . ' '
                . $appointment->lead->customer->zip_code . ' in 15 minutes  ' . URL::to('/') . '/dashboard/lead/' . $appointment->lead->id);

            HelperController::email('Appointment in 15 minutes with ' . $appointment->lead->customer->first_name . ' ' . $appointment->lead->customer->last_name,
                'You have an appointment with ' . $appointment->lead->customer->last_name .
                ' @' . $appointment->lead->customer->street_address . ' '
                . $appointment->lead->customer->zip_code . ' in 15 minutes  ',
                URL::to('/') . '/dashboard/lead/' . $appointment->lead->id, $user, 'Lead');



        }


    }
}
