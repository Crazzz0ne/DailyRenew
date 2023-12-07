<?php


namespace App\Console\Commands\Crons\RoundRobin;


use App\Events\Backend\SalesFlow\RepAddedToLeadEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Http\Controllers\HelperController;
use App\Http\Resources\LeadRepsResource;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Appointment\Availability;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class CronCallCenterRoundRobin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:callCenterAssign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Payroll';

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
     * @return mixed
     */
    public function handle()
    {


        $appointments = Appointment::where('user_id', null)
            ->whereHas('lead', function ($q) {
                $q->where('source', 'call center');
            })->with('lead')->get();

        $roundRobinOfficeLists = RoundRobin::where('type', 'Call Center Appointments')
            ->where('office_id', null)
            ->pluck('list')
            ->first();
        $g = 0;
        $callCenterAppointments = [];
        $noMatchList = [];
        foreach ($appointments as $appointment) {


            $queue = Line::where('lead_id', $appointment->lead->id)->where('type', 'Call_Center_Appointment')->first();
            if (empty($queue)) {
                event(new TextEvent('6199406423', 'Call center RR failed Fix it! lEAD '. $appointment->lead_id));
                continue;
            }

            foreach ($roundRobinOfficeLists as $roundRobinOfficeId) {


                $officeRoundRobin = null;

                if ($roundRobinOfficeId !== 10) {
                    $office = Office::where('id', $roundRobinOfficeId)
                        ->withAllTagsOfAnyType(ucfirst($appointment->lead->customer->city))
                        ->first();
                } else {
                    $office = Office::where('id', $roundRobinOfficeId)
                        ->first();
                }

                if (empty($office)) {
                    //If There is no office set, Skip remainder of this iteration
                    continue;
                }
                $officeRoundRobin = RoundRobin::where('office_id', $roundRobinOfficeId)
                    ->where('office_id', $office->id)
                    ->where('type', 'Call Center Appointments')
                    ->first();

                if (empty($officeRoundRobin)) {
                    //If There is no officeRR set, Skip remainder of this iteration
                    continue;
                }


                $officeRoundRobinArray = $officeRoundRobin->list;
                foreach ($officeRoundRobinArray as $userId) {

                    if ($queue->filled_user_id) {
                        //If There is a filled_user_id, Skip remainder of this iteration
                        continue;
                    }

                    $user = User::findOrFail($userId);

                    if ($user->terminated) {
//                        \Illuminate\Support\Facades\Log::alert('Scout Tried to assign a terminated employee');
                        //If user is terminated remote-able, skip the remainder of this iteration.
                        continue;
                    }

                    if ($appointment->remote) {

                        if (!$user->remote_option) {

                            continue;
                        }
                    }

                    if (!$appointment->remote && $roundRobinOfficeId === 10) {
                        //Prevent RR for remote office.
                        //If appointment is not remote-able, and the office is a remote-only office,
                        // skip the remainder of this iteration.
                        continue;
                    }

                    if (!in_array($appointment->lead->customer->language, $user->languages)) {
                        //If user does not speak same language as the lead customer, skip the remainder of this iteration.
                        continue;
                    }


                    $availability = Availability::where('user_id', $user->id)
                        ->where(function ($q) use ($appointment) {
                            $q->where('start', '<=', $appointment->start_time);
                            $q->where('end', '>=', $appointment->start_time);
                        })->where(function ($q) use ($appointment) {
                            $q->where('start', '<=', $appointment->finish_time);
                            $q->where('end', '>=', $appointment->finish_time);
                        })->get();


                    $currentAppointment = Appointment::where('user_id', $user->id)
                        ->where(function ($q) use ($appointment) {
                            $q->where('start_time', '<=', $appointment->start_time);
                            $q->where('finish_time', '>=', $appointment->start_time);
                        })->where(function ($q) use ($appointment) {
                            $q->where('start_time', '<=', $appointment->finish_time);
                            $q->where('finish_time', '>=', $appointment->finish_time);
                        })->get();

//                    this is stupid but it doesnt work without it
                    $currentAppointmentCount = 0;
                    foreach ($currentAppointment as $appointment) {
                        $currentAppointmentCount = 1;
                    }
                    $currentAvailabilityCount = 0;
                    foreach ($availability as $appointment) {
                        $currentAvailabilityCount = 1;
                    }

//                    \Log::debug('availability count userId ' . $userId, [$availability]);
//                    \Log::debug('appointment count userId ' . $userId, [$currentAppointment]);


                    if ($currentAvailabilityCount == 0 && $currentAppointmentCount == 0) {


                        $body = "You have been scheduled for a close. " . Carbon::parse($appointment->start_time)->format('D F jS g:i a') . "\nZip " . $appointment->lead->customer->zip_code .
                            "\n Click the link below to view ";

                        $link = URL::to("/") . "/dashboard/lead/" . $appointment->lead->id;

                        if ($user->auto_assign_rr) {
                            $queue->update([
                                'filled_user_id' => $user->id,
                                'filled_time' => Carbon::now()->toDateTimeString()]);

                            $newRep = new UserHasLead();
                            $newRep->user_id = $user->id;
                            $newRep->lead_id = $appointment->lead->id;
                            $newRep->position_id = 3;
                            $newRep->save();

                            Lead::where('id', $appointment->lead->id)
                                ->update(['office_id' => $user->office_id]);

                            $rep = new LeadRepsResource($newRep);
                            event(new RepAddedToLeadEvent($rep, $appointment->lead->id));
                            \Log::debug('Auto assigned' . $userId . ' appointment' . $appointment->id);
                            HelperController::email('New Close scheduled (' . $queue->lead_id . ') ', $body, $link, $user, 'Lead');
                            $appointment->update(['user_id' => $user->id,
                                'subject' => 'Close ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1)
                                    . '.  @' . $appointment->lead->customer->full_name]);
                        } else {
                            $body = "Want a lead? . " . Carbon::parse($appointment->start_time)->format('D F jS g:i a') .
                                "\n Click the link below to accept ";
                            $link = URL::to("/") . "/dashboard/lead/queue/" . $queue->id;
                            HelperController::email('Queue/SP2 ', $body, $link, $user, 'Queue');


                        }
                        unset($officeRoundRobinArray[array_search($userId, $officeRoundRobinArray)]);
                        array_push($officeRoundRobinArray, $userId);
                        $payloadArray = [];
                        foreach ($officeRoundRobinArray as $item) {
                            array_push($payloadArray, $item);
                        }

                        $officeRoundRobin->list = $payloadArray;
                        $officeRoundRobin->save();


                        unset($roundRobinOfficeLists[array_search($roundRobinOfficeId, $roundRobinOfficeLists)]);
                        array_push($roundRobinOfficeLists, $roundRobinOfficeId);

                        $payloadArray = [];
                        foreach ($roundRobinOfficeLists as $item) {
                            array_push($payloadArray, $item);
                        }
                        RoundRobin::where('type', 'Call Center Appointments')
                            ->where('office_id', null)
                            ->update(['list' => $payloadArray]);

                        $roundRobinOfficeLists = $payloadArray;

                        foreach ($appointments as $value) {
                            if ($appointment->id !== $value->id) {
                                array_push($callCenterAppointments, $value);

                            }
                        }

                        break 2;

                    } else {
                        $g++;
                    }
                }
            }
        }

//        checks to see if remote office can take leads




    }
}
