<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\SalesFlow\Lead\LeadUtility::class, function (Faker $faker) {
    $discountPlans = [
        'Medical Baseline',
        'Care',
        'FERA',
        'None'
    ];

    $ratePlans = [
        'TOU -D-4-9',
        'D-CARE',
        'Domestic',
    ];

    return [
        'kw_year_usage' => $faker->numberBetween(10000, 15000),
        'power_company' => strtoupper(Str::random(rand(2, 3))),
        'rate_plan' => $ratePlans[$faker->numberBetween(0, 2)],
        'power_discount_plan' => $discountPlans[$faker->numberBetween(0, 3)],
        'average_bill' => $faker->numberBetween(100, 300),
        'name_on_bill' => $faker->name,
        'deleted_at' => NULL,
        'power_company_id' => $faker->numberBetween(1, 30),
        'power_program_id' => $faker->numberBetween(1, 15),
        'power_discount_id' => $faker->numberBetween(1, 15),
    ];
});
