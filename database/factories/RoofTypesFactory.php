<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\RoofType;
use Faker\Generator as Faker;

$factory->define(RoofType::class, function (Faker $faker) {
    return [
        "name" => $faker->name
    ];
});
