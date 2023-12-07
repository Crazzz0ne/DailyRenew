<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Mastermind\MastermindCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => $faker->streetAddress,
    ];
});
