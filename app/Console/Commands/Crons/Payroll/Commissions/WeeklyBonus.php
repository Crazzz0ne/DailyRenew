<?php

namespace App\Console\Commands\Crons\Payroll\Commissions;

use Illuminate\Console\Command;

class WeeklyBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:WeeklyBonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creat Weekly Bonus';

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
