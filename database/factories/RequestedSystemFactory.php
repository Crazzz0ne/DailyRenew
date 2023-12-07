<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use Faker\Generator as Faker;

$factory->define(RequestedSystem::class, function (Faker $faker) {
    return [
        'lead_id' => Lead::all()->random()->id,
        'epc_finance_id' => \App\Models\Epc\EpcFinance::all()->random()->id || null,
        'epc_system_id' => null,
        'inverter_id' => 0,
        'modules_count' => 0,
        'modules_id' => 0,
        'system_size' => null,
        'monthly_payment' => null,
        'adders' => '[' . rand(1, 30) . ',' . rand(1, 30) . ']',
        'needed_by' => null,
        'system' => null,
        'offset' => 110,
        'solar_rate' => 0.18,
        'ppw' => '19.90',
        'roof_work' => rand(1,10),
        'approved' => $faker->dateTime,
        'sales_rep_note' => $faker->sentence,
        'pb_note' => $faker->sentence,
        'deleted_at' => null,
    ];
});
