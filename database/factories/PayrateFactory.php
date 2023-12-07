<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesFlow\Commissions\PayRate;
use Faker\Generator as Faker;

$factory->define(PayRate::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'deleted_at' => null
    ];
});
