<?php

use App\Models\Auth\User;
use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder.
 */
class UserTableSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Add the master administrator, user id of 1
        User::create([
            'first_name' => 'Chris',
            'last_name' => 'Furman',
            'email' => 'chris.furman@solcalenergy.com',
            'password' => 'secret',
            'phone_number' => '',
            'api_token' => 'S4WR02SxslsFe95RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
            'languages' => ['english'],
            'remote_option' => true,
            'remember_token' => 'admin',
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'last_login_ip' => '68.8.197.153',
            'confirmed' => true,
        ]);
//        // user id of 2
//        User::create([
//            'first_name' => 'Casey',
//            'last_name' => 'Strong',
//            'email' => 'casey.strong@solcalenergy.com',
//            'password' => 'secret',
//            'phone_number' => '',
//            'languages' => ['english'],
//            'remote_option' => true,
//            'api_token' => 'S4WR02SxMlsFe95RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//            'remember_token' => 'admin',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
        // user id of 3
        User::create([
            'first_name' => 'Shane',
            'last_name' => 'Montana',
            'email' => 'shanem@solcalenergy.com',
            'password' => 'secret',
            'remember_token' => 'executive',
            'api_token' => 'SsWR02SxMlsFe95RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
             'languages' => ['english'],
            'remote_option' => true,
            'last_login_ip' => '68.8.197.153',
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'confirmed' => true,
        ]);
// user id of 4
//        User::create([
//            'first_name' => 'Sp2',
//            'last_name' => 'LastName',
//            'email' => 'sp2@example.com',
//            'phone_number' => '',
//            'password' => 'secret',
//             'languages' => ['english'],
//            'remote_option' => true,
//            'api_token' => 'S4WR02SxMlsFA95RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//            'remember_token' => 'user',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//// user id of 5
//        User::create([
//            'first_name' => 'Sp1',
//            'last_name' => 'lastName',
//            'phone_number' => '',
//            'languages' => ['english', 'spanish'],
//            'remote_option' => true,
//            'api_token' => 'S4WR02SxMlsFR95RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//            'email' => 'sp1@example.com',
//            'password' => 'secret',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//// user id of 6
//        User::create([
//            'first_name' => 'Canvasser',
//            'last_name' => 'lastName',
//            'email' => 'Canvasser@example.com',
//            'api_token' => 'S4WR02SxMlsFe95RRzzw20zeXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//             'languages' => ['english'],
//            'remote_option' => true,
//            'password' => 'secret',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//        // user id of 7
//        User::create([
//            'first_name' => 'SalesRep',
//            'last_name' => 'lastName',
//            'email' => 'SalesRep@example.com',
//            'api_token' => 'S4WR02SxMlsFe98RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//             'languages' => ['english'],
//            'remote_option' => true,
//            'password' => 'secret',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//// user id of 8
//        User::create([
//            'first_name' => 'roof',
//            'last_name' => 'manager',
//            'email' => 'rassessor@example.com',
//            'api_token' => 'S4WR02SxMlsFe98RRzzw20gezk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//            'password' => 'secret',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//        // user id of 9
//        User::create([
//            'first_name' => 'manager',
//            'last_name' => 'manager',
//            'email' => 'manager@example.com',
//            'api_token' => 'S4WRq2SxMlsFe98RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//            'password' => 'secret',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//        /// user id of 10
//        User::create([
//            'first_name' => 'regional',
//            'last_name' => 'manager',
//            'email' => 'rmanager@example.com',
//            'api_token' => 'S4WR02SxMlsFe98RRzzw20geXk6tzVuexr6jYSSlWNFftO37EzpygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//            'password' => 'secret',
//            'last_login_ip' => '68.8.197.153',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//// user id of 11
//        User::create([
//            'first_name' => 'account',
//            'last_name' => 'manager',
//            'email' => 'amanager@example.com',
//            'password' => 'secret',
//            'last_login_ip' => '68.8.197.153',
//            'api_token' => 'S4WR02SxMlsFe98Rmzzw20geXk6tzVuexr6jYSSlWNFftO37EzKygRIdDNVYfiVmbbGM5dtWS7moP1JW',
//            'confirmation_code' => md5(uniqid(mt_rand(), true)),
//            'confirmed' => true,
//        ]);
//
//
//
//        factory(User::class, 100)->create();
        $this->enableForeignKeys();
    }
}
