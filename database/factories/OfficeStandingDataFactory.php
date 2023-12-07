<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */


use App\Models\OfficeStanding\OfficeStandingData;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(OfficeStandingData::class, function (Faker $faker) {


    return [
        'name' => $faker->randomElement(['Volume Glory', 'Efficiency Glory', 'PPW Glory']),
        'sdate' => Carbon::createFromTimestamp($faker->date()->getTimeStamp()),
        'user_id' => 6,
        'office_id' => $faker->numberBetween(1, 8),
    ];


});
