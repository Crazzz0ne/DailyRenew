<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */


use Faker\Generator as Faker;

$factory->define(App\Models\Office\Office::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'address' => $faker->streetAddress,
        'city' => $faker->city,
        'zip_code' => $faker->postcode,
        'State' => 'Ca',
        'phone_number' => $faker->phoneNumber,
        'email' => $faker->safeEmail,
        'market_id' => $faker->randomElement([1, 2])
    ];
});
