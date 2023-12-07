<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\SalesFlow\Lead\Lead;
use Faker\Generator as Faker;
$factory->define(\App\Models\SalesFlow\Lead\System\System::class, function (Faker $faker) {
    return [
        'epc_finance_id' => fn() => rand(1, 25),
        'epc_system_id' => fn() => rand(1, 3),
        'inverter_id'=> fn() => rand(1, 2),
        'modules_count' => fn() => rand(10, 30),
        'modules_id' => fn() => rand(1, 3),
        'system_size' => 500,
        'monthly_payment' => rand(90,300),
        'offset' => rand(90, 156),
        'solar_rate' => '.18',
        'ppw' => '4.17',
        'roof_work' => rand(0, 3000),
    ];
});
