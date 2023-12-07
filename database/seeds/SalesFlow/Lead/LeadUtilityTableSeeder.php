<?php

use App\Models\SalesFlow\Lead\LeadStatus;

class LeadUtilityTableSeeder extends \Illuminate\Database\Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $this->disableForeignKeys();
        factory(\App\Models\SalesFlow\Lead\LeadUtility::class, 10)->create();
        $this->enableForeignKeys();
    }

}
