<?php


class EpcTableSeeder extends \Illuminate\Database\Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        \App\Models\Epc\Epc::create([
            'name' => 'Complete',
        ]);

        \App\Models\Epc\Epc::create([
            'name' => 'La Solar',
        ]);

        \App\Models\Epc\EpcCreditStatus::create([
           'name' => 'Credit Not run',
            'epc_id' => 1
        ]);

        \App\Models\Epc\EpcCreditStatus::create([
            'name' => 'Pass',
            'epc_id' => 1
        ]);


        \App\Models\Epc\EpcCreditStatus::create([
            'name' => 'Manual',
            'epc_id' => 1
        ]);


        \App\Models\Epc\EpcCreditStatus::create([
            'name' => 'Credit Not run',
            'epc_id' => 2
        ]);

        \App\Models\Epc\EpcCreditStatus::create([
            'name' => 'Tier II',
            'epc_id' => 2
        ]);

        \App\Models\Epc\EpcCreditStatus::create([
            'name' => 'Tier I',
            'epc_id' => 2
        ]);

        \App\Models\Epc\EpcCreditStatus::create([
            'name' => 'Fail',
            'epc_id' => 1
        ]);

        \App\Models\Epc\EpcCreditStatus::create([
            'name' => 'Fail',
            'epc_id' => 2
        ]);
    }
}
