<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Auth\User as User;


$factory->define(\App\Models\SalesFlow\Appointment\Appointment::class, function (Faker $faker) {
    $start = $faker->dateTimeBetween($startDate = '1 day', $endDate = '2 weeks');
    return [
        'lead_id' => \App\Models\SalesFlow\Lead\Lead::all()->random()->id,
        'type_id' => \App\Models\SalesFlow\Appointment\AppointmentType::all()->random()->id,
        'user_id' => User::all()->random()->id,
        'start_time' => $start,
        'finish_time' => $faker->dateTimeBetween($start, $start->format('Y-m-d H:i:s')),
        'subject' => User::all()->random()->first_name,
        'comment' => $faker->realText(150),
    ];
});
