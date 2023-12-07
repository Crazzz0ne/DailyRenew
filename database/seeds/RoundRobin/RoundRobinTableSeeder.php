<?php


use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder.
 */
class RoundRobinTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Add the master administrator, user id of 1
        \App\Models\RoundRobin\RoundRobin::create([
            'list' => [1, 10],
            'type' => 'Call Center Appointments',
        ]);
        \App\Models\RoundRobin\RoundRobin::create([
            'list' => [1,2,3],
            'office_id' => 10,
            'type' => 'Call Center Appointments',
        ]);

        \App\Models\RoundRobin\RoundRobin::create([
            'list' => [1,2,3],
            'office_id' => 1,
            'type' => 'Call Center Appointments',
        ]);

    }
}

