<?php

use Illuminate\Database\Seeder;

class AppointmentAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\SalesFlow\Appointment\Availability::class, 100)->create();
    }
}
