<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Console\Command;

class OneCallCenterRule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CallCenterMerge';

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
        Lead::where('source', 'call center alpha')->update(['source' => 'call center']);
        User::where('office_id', 11)->update(['office_id' => 10]);
    }
}
