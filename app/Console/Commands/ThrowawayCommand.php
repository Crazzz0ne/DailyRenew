<?php

namespace App\Console\Commands;

use App\Events\Backend\SalesFlow\Lead\LeadClosedEvent;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use Illuminate\Console\Command;

class ThrowawayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:throwawayCommand';

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
        $customers = Customer::all();
        foreach ($customers as $customer){
            if ($customer->city == 'Tampa'){
                $customer->state = 'FL';
                $customer->save();
            }else{
                $customer->state = 'CA';
                $customer->save();
            }
        }
    }
}
