<?php


namespace App\Console\Commands\Crons;


use App\Events\Backend\SalesFlow\LeadEditEvent;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Log;

class CronReleaseLeadEdit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cronReleaseLeadEdit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Releases edit if no edit has been made in 5 minutes';

    protected $leadRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
//        $this->leadRepository = new LeadRepository();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $leads = Lead::where('editing', '=', true)
            ->where('updated_at', '>', Carbon::now()->subMinute(30)->toDateTimeLocalString())
            ->get();
        foreach ($leads as $lead){
            event(new LeadEditEvent($lead->id, false, 1, 'demiGod'));
            Log::info('edit cron ran');
        }

        Lead::where('editing', '=', true)
            ->where('updated_at', '<', Carbon::now()->minutes(-30)->toDateTimeLocalString())
            ->update(["editing" => false]);

    }
}
