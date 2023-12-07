<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use App\Models\Auth\OfficeHasUser;
use Faker\Generator as Faker;


$factory->define(\App\Models\Office\OfficeUser::class, function (Faker $faker, $b) {


    return [
        'user_id' => $faker->unique()->numberBetween($min = 1, $max = 29),
        'office_id' => $faker->numberBetween($min = 1, $max = 3)
    ];

});
