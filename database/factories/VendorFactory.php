<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Auth\Vendor;
use Faker\Generator as Faker;

$factory->define(\App\Models\VendorLink\Vendor::class, function (Faker $faker) {

    return [
        'company_name' => $faker->company,
        'is_active' => $faker->boolean,
        'picture' => 'partner/logo/V7iqGNz57OLhpjToqY3dK6sTnS3wsWQ9xn6B2RT9.png',
    ];
});
