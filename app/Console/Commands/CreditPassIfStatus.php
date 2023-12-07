<?php


namespace App\Console\Commands;


use App\Models\SalesFlow\Lead\Lead;

use Illuminate\Console\Command;

class CreditPassIfStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:creditPassifStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Payroll';

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

      Lead::whereIn('status_id', [6,7,8,9,10,11,12,13])->where('epc_id', 1)->update(['credit_status_id' => 2]);

    }
}
