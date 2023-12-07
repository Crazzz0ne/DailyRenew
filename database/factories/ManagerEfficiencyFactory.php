<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Links;
use Faker\Generator as Faker;

$factory->define(\App\Models\Office\ManagerEfficiency::class, function (Faker $faker) {
    return [
        'office_id' => $faker->numberBetween(1, 6),
        'user_id' => $faker->numberBetween(2, 4),
        'canvassaers_openers_closers_avg' => $faker->numberBetween(1, 30),
        'manager_avg' => $faker->numberBetween(1, 30),
        'other' => $faker->numberBetween(1, 30),
        'truth' => $faker->numberBetween(0, 1),
    ];
});
