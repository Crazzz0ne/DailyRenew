<?php

namespace App\Console\Commands;

use App\Models\Commission\CommissionLedgers;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Appointment\Availability;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixTimezonesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:timeZones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Time is a flat circle';

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
        $availability = Availability::all();
        foreach ($availability as $available) {
            $start = Carbon::parse($available->start . ' America/Los_Angeles')->tz('UTC');
            $end = Carbon::parse($available->end . ' America/Los_Angeles')->tz('UTC');
            $available->start = $start;
            $available->end = $end;
            $available->save();
        }


        $appointments = Appointment::all();

        foreach ($appointments as $appointment) {
            $start = Carbon::parse($appointment->start_time . ' America/Los_Angeles')->tz('UTC');
            $end = Carbon::parse($appointment->finish_time . ' America/Los_Angeles')->tz('UTC');
            $appointment->start_time = $start;
            $appointment->finish_time = $end;
            $appointment->save();
        }
    }


}
