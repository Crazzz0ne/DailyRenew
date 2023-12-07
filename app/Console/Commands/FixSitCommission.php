<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixSitCommission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:satcom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show me';

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

        $carbon = Carbon::now()->startOfMonth()->toDateTimeString();
        dump($carbon);
        $leads = Lead::whereHas('salesPacket', function ($q) {
            $q->where('sat', true);
        })->whereHas('appointments', function ($q) use ($carbon) {
            $q->where('type_id', 6);
            $q->where('start_time', '>', $carbon);
        })->where('origin_office_id', 10)->with('salesPacket', 'reps', 'appointments')
            ->get();

        foreach ($leads as $lead) {

            dump($lead->id);
//         $lead->reps->filter(function ($value, $key) {
//             $value->
//         })
        }

    }
}
