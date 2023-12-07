<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Auth\UserHasPosition::class, function (Faker $faker) {
    return [
        'user_id' => \App\Models\Auth\User::all()->random()->id,
        'position_id' => \App\Models\SalesFlow\Position\Position::all()->random()->id
    ];
});
