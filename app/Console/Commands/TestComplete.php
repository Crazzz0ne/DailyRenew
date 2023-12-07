<?php


namespace App\Console\Commands;


use App\Events\Backend\SalesFlow\Lead\LeadClosedEvent;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestComplete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:testComplete';

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
        $leads = Lead::whereHas('salesPacket', function ($q) {
            $q->where('cpuc_doc_signed', '!=', null);
        })->get();

        foreach ($leads as $lead) {
            $updateLead = Lead::where('id', $lead->id)->first();
            $updateLead->close_date = Carbon::parse($lead->salesPacket->cpuc_doc_signed)->timezone('America/los_angeles')->toDateString();
            $updateLead->save();
        }
    }
}
