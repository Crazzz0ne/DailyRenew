<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Announcement\Announcement;
use Faker\Generator as Faker;
use App\Models\Auth\User as User;


$factory->define(Announcement::class, function (Faker $faker) {
    return [
        'subject' => $faker->realText($faker->numberBetween(10,45)),
        'body' => $faker->realText(800),
        'user_id' => User::all()->random()->id,
        'color' => $faker->randomElement(['normal', 'green', 'yellow', 'red']),
        'sticky' => $faker->numberBetween(0, 1),
        'created_at' => $faker->dateTimeBetween($startDate = '-2 years', $endDate = 'now'),
    ];
});
