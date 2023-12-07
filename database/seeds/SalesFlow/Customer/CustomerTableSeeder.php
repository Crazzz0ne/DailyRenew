<?php

use Illuminate\Database\Seeder,
    App\Models\SalesFlow\Customer\Customer;

class CustomerTableSeeder extends Seeder
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
        factory(Customer::class, 50)->create();
        $this->enableForeignKeys();
    }
}
