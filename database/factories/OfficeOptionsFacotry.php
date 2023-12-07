<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */


use Faker\Generator as Faker;

$factory->define(App\Models\Office\OfficeOptions::class, function (Faker $faker) {
    return [
        'permissions' => ["team work","view backend","view commission","view printable","view reporting","view training"],
        'commission_plan' => null,
        'roles' => ["manager","canvasser","sp1","sp2"],
        'default_role' => 'canvasser',

    ];
});
