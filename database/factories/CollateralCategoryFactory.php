<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Collateral\CollateralCategory;
use Faker\Generator as Faker;

$factory->define(CollateralCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => $faker->streetAddress,
    ];
});
