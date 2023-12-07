<?php

use App\Models\Office\OfficeStanding;
use App\Models\Office\OfficeStandingData;
use Illuminate\Database\Seeder;


class OfficeStandingTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->disableForeignKeys();
        OfficeStanding::create([
            'name' => 'Volume Glory',
            'sdate' => '2019-01-31',
            'user_id' => '3',
            'approved' => '1'
        ]);

        OfficeStanding::create([
            'name' => 'Efficiency Glory',
            'sdate' => '2019-01-31',
            'user_id' => '3',
            'approved' => '1'
        ]);

        OfficeStanding::create([
            'name' => 'PPW Glory',
            'sdate' => '2019-01-31',
            'user_id' => '3',
            'approved' => '1'
        ]);

        OfficeStanding::create([
            'name' => 'Volume Glory',
            'sdate' => '2019-02-28',
            'user_id' => '3',
            'approved' => '1'
        ]);

        OfficeStanding::create([
            'name' => 'Efficiency Glory',
            'sdate' => '2019-02-28',
            'user_id' => '3',
            'approved' => '1'
        ]);

        OfficeStanding::create([
            'name' => 'PPW Glory',
            'sdate' => '2019-02-28',
            'user_id' => '3',
            'approved' => '1'
        ]);


        $this->enableForeignKeys();
    }
}


