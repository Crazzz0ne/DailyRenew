<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Faker\Generator as Faker;

$factory->define(\App\Models\SalesFlow\Lead\LeadNote::class, function (Faker $faker) {
    $leads = Lead::all();
    $users = User::all();

    return [
        'lead_id' => $leads->random()->id,
        'user_id' => $users->random()->id,
        'note' => $faker->realText(145),
    ];
});
