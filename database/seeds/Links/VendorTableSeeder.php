<?php

use Illuminate\Database\Seeder;

class VendorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\VendorLink\Vendor::create([
            'id' => 1,
            'company_name' => 'All',

        ]);
        $vendor = factory(\App\Models\VendorLink\Vendor::class, 6)->create();
    }
}
