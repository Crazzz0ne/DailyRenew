<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesFlow\Lead\UserHasLead;
use Faker\Generator as Faker;
use App\Models\Auth\User,
    App\Models\SalesFlow\Lead\Lead,
    App\Models\SalesFlow\Position\Position;

$factory->define(UserHasLead::class, function (Faker $faker) {
    return [
        'user_id' => User::all()->random()->id,
        'lead_id' => Lead::all()->random()->id,
        'position_id' => Position::all()->random()->id,
    ];
});
