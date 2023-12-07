<?php

use Illuminate\Database\Seeder,
    App\Models\SalesFlow\Position\Position;

class PositionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        Position::create([
            'name' => 'canvasser',
        ]);
        Position::create([
            'name' => 'sp1',
        ]);
        Position::create([
            'name' => 'sp2',
        ]);
        Position::create([
            'name' => 'integrations',
        ]);
        Position::create([
            'name' => 'sales rep',
        ]);
        Position::create([
            'name' => 'proposal builder',
        ]);
        Position::create([
            'name' => 'credit runner',
        ]);
        Position::create([
            'name' => 'sunrun runner',
        ]);
        Position::create([
            'name' => 'account manager',
        ]);
        Position::create([
            'name' => 'roof assessor',
        ]);

        factory(\App\Models\Auth\UserHasPosition::class, 23)->create();

        $this->enableForeignKeys();
        //
    }
}
