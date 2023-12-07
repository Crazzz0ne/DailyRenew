<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    public function run()
    {
//        \App\Category::create([
//            'name' => 'default',
//            'sort_order' => 1,
//            'active' => 1,
//            'description' => 'this is the default category',
//        ]);
        $category = factory(\App\Models\VendorLink\Category::class, 3)->create();
    }

}
