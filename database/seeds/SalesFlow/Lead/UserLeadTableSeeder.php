<?php

use App\Models\SalesFlow\Lead\UserHasLead;
use Illuminate\Database\Seeder;

class UserLeadTableSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        factory(UserHasLead::class, 20)->create();

        $this->enableForeignKeys();

    }


}
