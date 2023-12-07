<?php

use App\Models\SalesFlow\Lead\System\RequestedSystem;
use Illuminate\Database\Seeder;

class RequestedSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RequestedSystem::class, 150)->create();
    }
}
