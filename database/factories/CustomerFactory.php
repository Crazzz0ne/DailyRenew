<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesFlow\Customer\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'street_address' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => 'ca',
        'zip_code' => '92688',
        'home_phone' => '6195555555',
        'cell_phone' => '6195555555',
        'email' => $faker->unique(true)->safeEmail,
        'language' => $faker->randomElement(['english', 'spanish']),
    ];
});
