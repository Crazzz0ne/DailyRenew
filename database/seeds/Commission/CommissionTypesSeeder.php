<?php

use App\Models\Commission\CommissionTypes;

class CommissionTypesSeeder extends \Illuminate\Database\Seeder
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
        CommissionTypes::create([
            'name' => 'Credit Qualified'
        ]);
        CommissionTypes::create([
            'name' => 'Bonus Credit Qualified'
        ]);
        $this->enableForeignKeys();
    }
}
