<?php

namespace App\Console\Commands\Crons\RoundRobin;

use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\Office\Office;
use Illuminate\Console\Command;

class UpdateAvalablity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:requestUpdate';

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

        $offices = Office::where('id', '>=', 10)->where('id', '!=', 14)->with('User')->get();

        $body = "Can you please update your availability for at least two weeks.
         scout.solar/dashboard/calendar/time-on/request";
        $i =0;
        $names = [];
       foreach ($offices as $office) {
           foreach ($office->user as $user) {
               if ($user->hasRole('sp2') && $user->id != 3 && $user->terminated == null) {
                   TwilioSMSController::sendSms($user->phone_number, $body);
                   $i++;

                   array_push($names, $user->fullname);
               }else{
//                   dd($user);
               }
           }
       }

    }
}


