<?php

use App\Models\Epc\EpcAdders;

class AddersTableSeeder extends \Illuminate\Database\Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        EpcAdders::create([
            'name' => '3 Story Roof',
            'cost' => 1450,
            'epc_id' => 2
        ]);


        EpcAdders::create([
            'name' => '33 Degree Tilt',
            'cost' => 1200,
            'epc_id' => 2
        ]);


        EpcAdders::create([
            'name' => 'Attic Run',
            'cost' => 550,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'B3 Breaker',
            'cost' => 950,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Bird Net',
            'cost' => 550,
            'epc_id' => 2
        ]);


        EpcAdders::create([
            'name' => 'CURB consumption monitoring',
            'cost' => 699,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'EV Charger',
            'cost' => 1399,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'EV Outlet',
            'cost' => 899,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Gas Relocation',
            'cost' => 2650,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'GMA (Renewable Energy Meter)',
            'cost' => 1100,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Ground Mount-Texas',
            'cost' => 2400,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'LADBS - Ballast system',
            'cost' => 1500,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Relocation',
            'cost' => 1850,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Upgrade 2 Gang Panel',
            'cost' => 3800,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Upgrade Meter Socket 400A',
            'cost' => 7100,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Upgrade 2 Gang Panel 400A',
            'cost' => 5200,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Upgrade 200A-Socal',
            'cost' => 2499,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Upgrade 200A-Norcal',
            'cost' => 3499,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Upgrade 400A',
            'cost' => 4200,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Nest Thermostat',
            'cost' => 690,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Custom',
            'cost' => null,
            'epc_id' => 2
        ]);




        EpcAdders::create([
            'name' => '3 Story Roof',
            'cost' => 1450,
            'epc_id' => 2
        ]);


        EpcAdders::create([
            'name' => '33 Degree Tilt',
            'cost' => 1200,
            'epc_id' => 2
        ]);


        EpcAdders::create([
            'name' => 'Attic Run',
            'cost' => 550,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'B3 Breaker',
            'cost' => 950,
            'epc_id' => 2
        ]);

        EpcAdders::create([
            'name' => 'Bird Net',
            'cost' => 550,
            'epc_id' => 2
        ]);


        EpcAdders::create([
            'name' => 'Main Panel Upgrade- Required',
            'cost' => 1800,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'Main Panel Upgrade- Not Required',
            'cost' => 2500,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'RMA/GMA Line side tap',
            'cost' => 800,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'System smaller than 3.5kw',
            'cost' => 500,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'Ground Mount',
            'cost' => .6,
            'flat_cost' =>false,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'Attic Run',
            'cost' => 500,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'Solar Skirt Guard',
            'cost' => 500,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'Ballast',
            'cost' => .4,
            'flat_cost' => false,
            'epc_id' => 1
        ]);

        EpcAdders::create([
            'name' => 'LG Chem',
            'cost' => 11100,
            'epc_id' => 1
        ]);



    }
}
