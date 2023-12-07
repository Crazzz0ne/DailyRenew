<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\SalesFlow\Lead\Lead;
use Faker\Generator as Faker;
$factory->define(\App\Models\Office\Team::class, function (Faker $faker) {
    return [
        'name' => fn() => rand(1, 25),
        'office_id' => fn() => rand(1, 3),
    ];
});
