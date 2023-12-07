<?php


namespace App\Console\Commands\Crons;


use App\Models\Commission\CommissionLedgers;
use App\Models\Commission\Payroll;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CanvasserGoBack  extends Command
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



    }
}
