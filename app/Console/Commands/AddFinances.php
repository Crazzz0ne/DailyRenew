<?php

namespace App\Console\Commands;

use App\Models\Commission\CommissionLedgers;
use App\Models\Epc\Epc;
use App\Models\Epc\EpcFinance;
use App\Models\Epc\Finance;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Appointment\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddFinances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:addFinances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds finances ';

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

        $finance = new Finance();
        $finance->name = 'Sun Run';
        $finance->save();

        $finance = new Finance();
        $finance->name = 'Bright Oak';
        $finance->save();

        $finance = new Finance();
        $finance->name = 'EverBright';
        $finance->save();

        EpcFinance::whereIn('id', [8,9,10,146,144,145,148,])->update(['finance_id' => 1]);
        EpcFinance::whereIn('id', [152, 153, 154,155,159,160])->update(['finance_id' => 2]);
        EpcFinance::whereIn('id', [156, 157])->update(['finance_id' => 2]);
        }

}

