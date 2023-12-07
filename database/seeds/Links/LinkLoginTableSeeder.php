<?php

use Illuminate\Database\Seeder;
use App\Models\VendorLink\LinkLogin;

class LinkLoginTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    use DisableForeignKeys;

    public function run()
    {

        LinkLogin::create([
            'user_name' => 'default',
            'password' => 'xyz_youKnowMe',
            'link_id' => 1,
        ]);
        LinkLogin::create([
            'user_name' => 'default',
            'password' => 'xyz_youKnowMe',
            'link_id' => 2,
        ]);
        LinkLogin::create([
            'user_name' => 'default',
            'password' => 'xyz_youKnowMe',
            'link_id' => 3,
        ]);
    }
}
