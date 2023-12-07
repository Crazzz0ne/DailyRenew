<?php

use Illuminate\Database\Seeder;
use App\Models\VendorLink\Link;

class LinkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

//        Link::create([
//            'representative' => 'default',
//            'sort_id' => 1,
//            'email' => 'email@email.com',
//            'office_phone' => '619-940-5555',
//            'cell_phone' => '555-555-5555',
//            'vendor_id' => 1,
//            'category_id' => 1,
//            'active' => 1,
//
//        ]);
        $links = factory(Link::class, 100)->create();
        $this->enableForeignKeys();
    }
}
