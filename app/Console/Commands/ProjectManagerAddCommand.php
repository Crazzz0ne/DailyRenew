<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use Illuminate\Console\Command;

class ProjectManagerAddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:attachProjectManager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $leads = Lead::where('close_date', '!=', null)->whereHas('reps', function ($q) {
            $q->where('position_id', 9);
            $q->where('user_id', 12);
        })->with(['reps' => function ($q) {
            $q->where('position_id', 9);
            $q->where('user_id', 12);
        }])->get();
        $projectCoordinator = [479, 17];
        foreach ($leads as $lead) {
            $userId = array_shift($projectCoordinator);
            UserHasLead::where('lead_id', $lead->id)->where('position_id', 9)->where('user_id', 12)->update(['user_id' => $userId]);
            $projectCoordinator[] = $userId;
        }
    }
}
