<?php


namespace App\Console\Commands;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Models\Commission\CommissionLedgers;
use App\Models\Commission\Payroll;
use App\Models\SalesFlow\MassText\MassText;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TwillioMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createPayroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Text';

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



    }
}
