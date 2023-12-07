<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Console\Command;

class changeOfficeSp2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:changeOfficeSp2';

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


    public function handle()
    {
        $leads = Lead::whereHas('user', function ($q){
            $q->where('user_id', 322);
        })->get();
        foreach ($leads as $lead) {
            $lead->update(['origin_office_id' => 62]);
        }
    }
}
