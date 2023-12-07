<?php


namespace App\Console\Commands;


use App\Models\SalesFlow\Lead\Lead;

use Carbon\Carbon;
use Illuminate\Console\Command;

class DevPayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:DevPay';

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
        $pendingInstall = [6,7,8,9,10,11];
        $start = Carbon::now()->subMonths()->toDateString();
        $leads = Lead::where('close_date', '!=', null)
            ->where('origin_office_id', 10)
            ->wherein('status_id', $pendingInstall)
            ->where('created_at', '>', $start)
            ->with('system')
            ->get();
        $totalKwh = 0;
        $count = 0;
        foreach ($leads as $lead) {
            $totalKwh = $lead->system->system_size + $totalKwh;
            $count++;
        }
        dump($count);

        dump($totalKwh);

    }
}
