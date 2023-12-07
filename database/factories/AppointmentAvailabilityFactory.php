<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Availability;
use Faker\Generator as Faker;

$factory->define(\App\Models\SalesFlow\Appointment\Availability::class, function (Faker $faker) {
    return [
        'user_id' => User::all()->random()->id,
        'start' => $faker->dateTime,
        'end' => $faker->dateTime,
        'approved' => rand(0, 1),
        'reviewed' => rand(0, 1),
        'deleted_at' => null,
    ];
    //After we've created the model, let's check to make sure it can't be approved, without having also been marked as reviewed.
})->afterCreating(Availability::class, function (Availability $availability) {
    if ($availability->approved && !$availability->reviewed) {
        $availability->update([
            'reviewed' => 1
        ]);
    }
});
