<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ListOUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes JIJ error';

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

        Lead::where('origin_office_id', 33)->update(['origin_office_id' => 34]);
        Lead::where('office_id', 33)->update(['office_id'=> 34]);
    }

}
