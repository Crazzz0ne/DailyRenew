<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Auth\User;
use App\Models\Commission\CommissionLedgers;
use App\Models\Office\Office;
use App\Models\SalesFlow\Lead\Lead;
use Faker\Generator as Faker;

$factory->define(CommissionLedgers::class, function (Faker $faker) {
    return [
        'user_id' => User::all()->random()->id,
        'lead_id' => Lead::all()->random()->id,
        'office_id' => Office::all()->random()->id,
        'type_id' => rand(1,2),
        'amount' => rand(1, 250),
        'deleted_at' => null,
        'manual' => false,
        'approved' => true,
    ];
});
