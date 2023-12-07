<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Collateral\CollateralContent;
use Faker\Generator as Faker;

$factory->define(CollateralContent::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => 'Here is a description of what is going on with this content',
        'category_id' => $faker->numberBetween(1, 6),
        'user_id' => $faker->numberBetween(1, 2),
        'state' => 'CA',
        'vendor_id' => 1,
        'path' => '/storage/app/public/training/pdf/Ratke-McClure-test1.pdf'
    ];
});
