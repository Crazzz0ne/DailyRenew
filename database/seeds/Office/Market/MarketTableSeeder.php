<?php

use App\Models\Office\Market\Market;
use App\Models\Office\Market\PowerCompany\PowerCompany;
use App\Models\Office\Market\PowerCompany\Program;
use Illuminate\Database\Seeder;

class MarketTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Market::create([
            'name' => 'san diego',
            'abbreviation' => 'SD',
        ]);
        Market::create([
            'name' => 'antelope valley',
            'abbreviation' => 'AV',
        ]);
        Market::create([
            'name' => 'sub dealership',
            'abbreviation' => 'AV',
        ]);
    }
}
