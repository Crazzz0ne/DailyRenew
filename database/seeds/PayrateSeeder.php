<?php

use App\Models\SalesFlow\Commissions\PayRate;
use Illuminate\Database\Seeder;

class PayrateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(PayRate::class, 10)->create();
    }
}
