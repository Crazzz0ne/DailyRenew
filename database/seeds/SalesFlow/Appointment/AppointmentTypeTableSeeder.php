<?php

use Illuminate\Database\Seeder;
use App\Models\SalesFlow\Appointment\AppointmentType;

class AppointmentTypeTableSeeder extends Seeder
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
        AppointmentType::create([
            'name' => 'canvasser'
        ]);

        AppointmentType::create([
            'name' => 'sp1'
        ]);

        AppointmentType::create([
            'name' => 'sp2'
        ]);

        AppointmentType::create([
            'name' => 'site survey'
        ]);

        AppointmentType::create([
            'name' => 'install'
        ]);

        AppointmentType::create([
            'name' => 'close'
        ]);

        AppointmentType::create([
            'name' => 'follow up'
        ]);


        $this->enableForeignKeys();
    }
}
