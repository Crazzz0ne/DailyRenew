<?php

namespace App\Console\Commands;

use App\Models\Commission\CommissionLedgers;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixCallCenterPayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fixCallCenter';

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
        $leads = Lead::where('origin_office_id', 33)
            ->where('credit_status_id', 2)
            ->where('created_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
            ->whereHas('appointments', function ($query) {
                $query->where('type_id', '=',6);
            })
            ->with('reps', 'appointments')
            ->get();

        foreach ($leads as $lead) {
            if ($lead->credit_status_id == 2) {
                foreach ($lead->reps as $rep) {

                    if ($lead->appointments) {
                        foreach ($lead->appointments as $appointment) {
                            if ($appointment->type_id == 6) {

                                if ($rep->pivot->position_id == 1) {
                                    $pay = 20;
                                } else if ($rep->pivot->position_id == 2) {
                                    $pay = 25;
                                } else {
                                    continue;
                                }
                                $checkCommission = CommissionLedgers::where('lead_id', $lead->id)
                                    ->where('type_id', 1)
                                    ->where('user_id', $rep->id)
                                    ->count();
                                if ($checkCommission) {
                                    $commission = new CommissionLedgers();
                                    $commission->type_id = 1;
                                    $commission->lead_id = $lead->id;
                                    $commission->amount = $pay;
                                    $commission->office_id = $lead->origin_office_id;
                                    $commission->user_id = $rep->id;
                                    $commission->save();
                                }


                            }
                        }
                    }
                }
            }
        }
        dd($leads[0]->reps[0]->pivot);
    }


}
