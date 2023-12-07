<?php


namespace App\Console\Commands\Crons\Payroll;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\Commission\CommissionLedgers;
use App\Models\Commission\Payroll;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CreateBiWeeklyPayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createBiWeeklyPayroll';

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

        $today = Carbon::today()->timezone('America/los_angeles')->format('d');
        $start = (int)$today;
        if (false) {
//            $start !== 01

            $start = Carbon::now()->timezone('America/los_angeles')->month(10)->day(15);
            $end = $start->copy()->endOfMonth();

            Log::info('Start of period ' . $start->toDateTimeString());
            Log::info('End of period ' . $end->toDateTimeString());
        } else {
            $start = Carbon::now()->startOfMonth()->hour(7);
            $end = $start->copy()->day(15)->hour(7);
            Log::info('Start of period ' . $start->toDateTimeString());
            Log::info('End of period ' . $end->toDateTimeString());
        }

        $commissions = CommissionLedgers::whereBetween('created_at', [$start, $end])
            ->where('office_id', 10)
            ->get();

        $groups = $commissions->groupBy('user_id');

        $payLoad = [];

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

        }

    }
}
