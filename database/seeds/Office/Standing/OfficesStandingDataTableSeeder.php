<?php


use App\Models\Office\OfficeStandingData;
use Illuminate\Database\Seeder;

class OfficesStandingDataTableSeeder extends Seeder
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
        OfficeStandingData::create([
            'name' => 'closes',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '1',
        ]);

        OfficeStandingData::create([
            'name' => 'm_installs',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '1',
        ]);

        OfficeStandingData::create([
            'name' => 'y_installs',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '1',
        ]);

        OfficeStandingData::create([
            'name' => 'closes',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '1',
        ]);

        OfficeStandingData::create([
            'name' => 'm_installs',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '1',
        ]);

        OfficeStandingData::create([
            'name' => 'y_installs',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '1',
        ]);


        OfficeStandingData::create([
            'name' => 'closes',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '1',
        ]);

        OfficeStandingData::create([
            'name' => 'm_installs',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '1',
        ]);

        OfficeStandingData::create([
            'name' => 'y_installs',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '1',
        ]);

        ///Efficiency Glory
        ///
        OfficeStandingData::create([
            'name' => 'closes',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Installs',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Yearly Rank',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Ave Score',
            'data' => (rand(1, 20)) / 4,
            'office_id' => '1',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Score',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '2',
        ]);

        //
        OfficeStandingData::create([
            'name' => 'closes',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Installs',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Yearly Rank',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Ave Score',
            'data' => (rand(1, 20)) / 4,
            'office_id' => '2',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Score',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '2',
        ]);

        /////
        ///
        OfficeStandingData::create([
            'name' => 'closes',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Installs',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Yearly Rank',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Ave Score',
            'data' => (rand(1, 20)) / 4,
            'office_id' => '3',
            'office_standing_id' => '2',
        ]);

        OfficeStandingData::create([
            'name' => 'Score',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '2',
        ]);

        ///PPW Glory
        ///
        ///
        OfficeStandingData::create([
            'name' => 'Yearly Rank',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '3',
        ]);

        OfficeStandingData::create([
            'name' => 'Ave Score',
            'data' => (rand(1, 20)) / 4,
            'office_id' => '1',
            'office_standing_id' => '3',
        ]);

        OfficeStandingData::create([
            'name' => 'Score',
            'data' => rand(1, 20),
            'office_id' => '1',
            'office_standing_id' => '3',
        ]);

        //
        OfficeStandingData::create([
            'name' => 'Yearly Rank',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '3',
        ]);

        OfficeStandingData::create([
            'name' => 'Ave Score',
            'data' => (rand(1, 20)) / 4,
            'office_id' => '2',
            'office_standing_id' => '3',
        ]);

        OfficeStandingData::create([
            'name' => 'Score',
            'data' => rand(1, 20),
            'office_id' => '2',
            'office_standing_id' => '3',
        ]);

        ///
        ///

        OfficeStandingData::create([
            'name' => 'Yearly Rank',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '3',
        ]);

        OfficeStandingData::create([
            'name' => 'Ave Score',
            'data' => (rand(1, 20)) / 4,
            'office_id' => '3',
            'office_standing_id' => '3',
        ]);

        OfficeStandingData::create([
            'name' => 'Score',
            'data' => rand(1, 20),
            'office_id' => '3',
            'office_standing_id' => '3',
        ]);


        $this->enableForeignKeys();
    }
}
