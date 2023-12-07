<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesFlow\Lead\Lead;
use Faker\Generator as Faker;

$factory->define(\App\Models\SalesFlow\Lead\LeadLogin::class, function (Faker $faker) {
    return [
        'user_name' => $faker->email,
        'password' => $faker->password,
    ];
});
