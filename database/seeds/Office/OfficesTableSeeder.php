<?php


use Illuminate\Database\Seeder;

class OfficesTableSeeder extends Seeder
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
        $office = factory(\App\Models\Office\Office::class, 50)->create()->each(function ($office){
            $office->options()->save(factory(\App\Models\Office\OfficeOptions::class)->make());
            $office->teams()->save(factory(\App\Models\Office\Tea::class)->make());
        });

        $this->enableForeignKeys();

    }
}
