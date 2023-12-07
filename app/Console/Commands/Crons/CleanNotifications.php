<?php

namespace App\Console\Commands\Crons;

use App\Models\Notifications\Notification;
use Illuminate\Console\Command;

class CleanNotifications  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clearOldNotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Weekly Payroll';

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
        Notification::whereDate( 'created_at', '<=', now()->subDays( 7))->delete();
    }
}
