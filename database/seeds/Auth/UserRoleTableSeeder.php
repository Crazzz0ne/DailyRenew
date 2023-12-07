<?php

use App\Models\Auth\User;
use Illuminate\Database\Seeder;

/**
 * Class UserRoleTableSeeder.
 */
class UserRoleTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        User::find(1)->assignRole(config('access.users.admin_role'));
//        User::find(2)->assignRole(config('access.users.admin_role'));
        User::find(2)->assignRole(config('access.users.executive_role'));
//        User::find(4)->assignRole('sp1');
//        User::find(5)->assignRole('sp2');
//        User::find(6)->assignRole('canvasser');
//        User::find(7)->assignRole('sales rep');
//        User::find(8)->assignRole('roof assessor');

//        User::find(4)->assignRole(config('access.users.manager_role'));

//        $user = User::where('id', '>', 7)->get();
//        foreach ($user as $u) {
//            $u->assignRole(config('access.users.default_role'));
//        }
//        User::find(5)->assignRole(config('access.users.super_role'));
//        User::find(6)->assignRole(config('access.users.super_role'));
//        User::find(7)->assignRole(config('access.users.executive_role'));


        $this->enableForeignKeys();
    }
}
