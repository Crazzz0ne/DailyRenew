<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Training\TrainingCategory;
use Faker\Generator as Faker;

$factory->define(TrainingCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'description' => $faker->streetAddress,
        //
    ];
});
