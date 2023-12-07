<?php

namespace App\Console\Commands;

use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Console\Command;
use function GuzzleHttp\Promise\all;

class CleanNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:cleanNumbers';

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
        $customers = Customer::all();

        foreach ($customers as $customer) {
            if (str_contains($customer->cell_phone, '+1')) {
//               dump(substr($customer->cell_phone, 2));
               $customer->update(['cell_phone' => substr($customer->cell_phone, 2)]);
            }elseif (str_starts_with('1', $customer->cell_phone)) {
                $customer->update(['cell_phone' => substr($customer->cell_phone, 1)]);
            }else{
                $data = preg_replace('/[^0-9]/', '', $customer->cell_phone);
                $customer->update(['cell_phone' => $data]);
            }
//            $data = preg_replace('/[^0-9]/', '', $customer->cell_phone);
//            $customer->update(['cell_phone' => $data]);
        }


    }
}
