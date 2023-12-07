<?php

use App\Models\Office\OfficeCommissions;

class OfficeCommissionsSeeder extends \Illuminate\Database\Seeder
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
        OfficeCommissions::create([
            'type_id' => 1,
            'office_id' => 1,
            'amount' => 100,
        ]);

        OfficeCommissions::create([
            'type_id' => 2,
            'office_id' => 1,
            'amount' => 100,
        ]);
        $this->enableForeignKeys();
    }
}
