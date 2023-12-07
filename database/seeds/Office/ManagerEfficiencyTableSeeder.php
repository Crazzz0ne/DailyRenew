<?php

use Illuminate\Database\Seeder;


class ManagerEfficiencyTableSeeder extends Seeder
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
        $office = factory(\App\Models\Office\ManagerEfficiency::class, 10)->create();
        $this->enableForeignKeys();
    }

}
