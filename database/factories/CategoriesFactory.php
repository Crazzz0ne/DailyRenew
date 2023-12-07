<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\VendorLink;
use Faker\Generator as Faker;

$factory->define(VendorLink\Category::class, function (Faker $faker) {
    static $number = 1;
    return [
        'active' => true,
        'sort_order' => $number++,
        'name' => $faker->unique()->randomElement(['finance', 'install', 'technical support']),
        'description' => $faker->realText(),
//	    'address' => $faker->address,
    ];
});
