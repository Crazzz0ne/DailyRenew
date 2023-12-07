<?php

namespace App\Console\Commands\Crons\Payroll;

use App\Models\Commission\CommissionLedgers;
use App\Models\Commission\Payroll;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CallCenterWeeklyPayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:WeeklyPayroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Weekly Payroll';

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


        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateTimeString();
        $commissions = CommissionLedgers::where('created_at', '>', $sevenDaysAgo)
            ->get();
        $groups = $commissions->groupBy('user_id');


        foreach ($groups as $key => $group) {
            $payLoad[$key]['id'] = [];
            $total = 0;
            foreach ($group as $commission) {
                array_push($payLoad[$key]['id'], $commission->id);
                $total += $commission->amount;
            }
            $payLoad[$key]['total'] = $total;
        }


        $p = collect($payLoad);

        Log::info('Payroll Payload: ' . json_encode([$p]));

        foreach ($p as $key => $load) {
            $payroll = new Payroll();
            $payroll->user_id = $key;
            $payroll->amount = $load['total'];
            $payroll->commissions = $load['id'];
            $payroll->save();
            dump($load);
        }

    }
}
