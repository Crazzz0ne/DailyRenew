<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Links;
use Faker\Generator as Faker;

$factory->define(\App\Models\VendorLink\Link::class, function (Faker $faker) {
    static $number = 1;
    return [
        'active' => true,
        'representative' => $faker->name,
        'email' => $faker->safeEmail,
        'notes' => $faker->text('60'),
        'office_phone' => $faker->numerify('###-###-####'),
        'cell_phone' => $faker->numerify('###-###-####'),
        'vendor_id' => $faker->numberBetween(2, 6),
        'category_id' => $faker->numberBetween(1, 6),
//	    'address' => $faker->address,
    ];
});
