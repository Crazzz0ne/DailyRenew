<?php


namespace App\Console\Commands\Crons\RoundRobin;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\Auth\User;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Line;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class GoBack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:goBack';

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

        $goBacks = Line::where('type', 'Go Back')
            ->where('filled_user_id', '=', null)
            ->whereHas('lead.appointments', function ($q) {
                $twentyMinutes = Carbon::now()->shiftTimezone('losangles/')->addHours(24);
                $q->where('type_id', 9);
                $q->where('start_time', '<', $twentyMinutes);

            })
            ->with('lead.appointments', 'lead.customer')->get();

        $roundRobin = RoundRobin::where('type', 'Call Center Appointments')->where('office_id', null)->get();
        foreach ($goBacks as $followup) {

            $list = $roundRobin->where('office_id', $followup->lead->office_id)->first();
            $storeForLater = [];
            foreach ($list->list as $eligibleUser) {

                $filledTime = Appointment::where('user_id', $eligibleUser)
                    ->whereBetween('start_time', [$followup->lead->appointment[0]->start_time, $followup->lead->appointment[0]->end_time])
                    ->count();

                $array = $list->list;

                if ($filledTime === 0) {
                    $user = User::find($eligibleUser);


                    $body = "Scheduled Go back @" . $followup->lead->appointments[0]->start_time . "\nZip " . $followup->lead->customer->zip_code . " "
                        . URL::to("/") . "/dashboard/lead/queue/" . $followup->id;
//                    event(new TextEvent($user->phone_number, $body));



                    array_shift($array);
                    array_push($array, $user->id);

                    $list->list = $array;
                    $list->save();
                    break;

                } else {
                    array_push($storeForLater, $eligibleUser);
                    array_shift($array);
                    $list->list = $array;
                    $list->save();
                }

            }

            $storeForLater = array_reverse($storeForLater);

            foreach ($storeForLater as $later) {
                array_unshift($array, $later);
                $list->list = $array;
                $list->save();
            }

        }

//        \Log::debug('this is running gobacks', []);

    }
}
