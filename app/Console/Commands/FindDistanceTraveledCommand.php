<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FindDistanceTraveledCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:findDistanceTraveled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'finds the distance traveled for each lead';


    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();

    }



    public function handle()
    {

//        find lead whereHas $user->id
        $leads = Lead::whereHas('user', function ($query) {
            $query->where('id', 1);
        })->get();
dd($leads);
        $nicksAddress = '24602 Town Center Dr 3207, Santa clarita, CA 91355 US';
        $totalDistance = 0;
        $totalTime = 0;
      $lastMonth = Carbon::now()->startOfMonth();

        $leads = Lead::whereHas('user', function ($q) {
            $q->where('user_id', 111);
        })->whereHas('appointment', function ($q) use ($lastMonth) {
            $q->where('start_time', '<', $lastMonth);

        })->where('origin_office_id', 60)->with('customer')->get();



        foreach ($leads as $lead) {
            $address = $lead->customer->address . ' ' . $lead->customer->city . ' ' . $lead->customer->state . ' ' . $lead->customer->zip;
      $trip =   $this->getDistance($nicksAddress, $address);
      $totalTime += $trip['time'] *2;
        $totalDistance += $trip['distance'] * 2;

        }

        dd($totalTime, $totalDistance);
    }

    function getDistance($nicksAddress, $address)
    {
        $nicksAddress = str_replace(" ", "+", $nicksAddress);
        $address = str_replace(" ", "+", $address);

        $json = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=$nicksAddress&destinations=$address&key=AIzaSyBI1SbiL5SfJX-MqyMc1b2helnjjKuolk8");
        $json = json_decode($json);
return [ 'distance' => $json->rows[0]->elements[0]->distance->value, 'time' => $json->rows[0]->elements[0]->duration->value];




    }
}
