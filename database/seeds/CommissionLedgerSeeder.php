<?php

use App\Models\Commission\CommissionLedgers;
use Illuminate\Database\Seeder;

class CommissionLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(CommissionLedgers::class, 25)->create();
    }
}
