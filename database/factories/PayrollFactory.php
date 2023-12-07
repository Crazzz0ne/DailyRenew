<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Auth\User;
use App\Models\Commission\Payroll;
use Faker\Generator as Faker;

$factory->define(Payroll::class, function (Faker $faker) {
    return [
        'user_id' => User::all()->random()->id,
        'commissions' => $faker->sentence,
        'amount' => rand(1,250),
        'deleted_at' => null,
    ];
});
