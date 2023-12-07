<?php

use Illuminate\Database\Seeder,
    App\Models\SalesFlow\Appointment\Appointment;

class AppointmentTableSeeder extends Seeder
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
        factory(Appointment::class, 100)->create();
        $this->enableForeignKeys();
    }
}
