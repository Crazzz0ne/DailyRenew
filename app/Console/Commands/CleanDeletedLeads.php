<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanDeletedLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:CleanDeletedLeads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'format Numbers Properly';

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
        $leads = Lead::all();

        foreach ($leads as $lead) {

            if ($lead->deleted_at) {
                $customer = Customer::where('id', $lead->customer_id)->first();
                dump($customer->id);
                $customer->delete();
//                dump($customer);
            }else{

            }

        }


    }
}
