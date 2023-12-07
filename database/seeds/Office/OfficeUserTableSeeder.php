<?php

use Illuminate\Database\Seeder;


class OfficeUserTableSeeder extends Seeder
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
        $officeHasUser = factory(\App\Models\Office\OfficeUser::class, 29)->create();

        $this->enableForeignKeys();
    }
}


