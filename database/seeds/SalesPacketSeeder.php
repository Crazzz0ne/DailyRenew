<?php

use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\SalesPacket;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesPacketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(SalesPacket::class, 25)->create();
    }
}
