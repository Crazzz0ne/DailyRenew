<?php

namespace App\Console\Commands\Crons;

use App\Events\Backend\SalesFlow\LeadEditEvent;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Outside\ReHash;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Log;

class MakeStaleCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:makeStale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Leads Go Stale, Should mark them right?';

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


    public function handle()
    {

        $leads = Lead::where('id', '>', 1)
            ->where('stale', null)
            ->with('appointments', 'leadNote', 'customer')
            ->get();
        foreach ($leads as $lead) {
            $triggered = false;
            if ($lead->status_id === 24 || $lead->status_id === 13 || $lead->status_id === 12 || $lead->status_id === 11 ||
                $lead->status_id === 10 || $lead->status_id === 9 || $lead->status_id === 8) {
                continue;
            }
            $weeksAgo = Carbon::now()->subWeeks(3);
            foreach ($lead->appointments as $appointment) {
                if ($weeksAgo->lessThan($appointment->start_time)) {
                    $triggered = true;
                }
            }
            foreach ($lead->leadNote as $note) {
                if ($weeksAgo->lessThan($note->created_at)) {
                    $triggered = true;
                }
            }
            if (!$triggered) {
                $lead->stale = Carbon::now();
                $lead->save();
            } else {
                if (count($lead->appointments) === 0 || (count($lead->leadNote) === 0)) {
                    $weeksAgo = Carbon::now()->subWeeks(6);
                    if ($weeksAgo->greaterThan($lead->updated_at)) {
                        $lead->stale = Carbon::now();
                        $lead->save();
                    }
                }
            }
        }
    }
}
