<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SalesFlow\Lead\SalesPacket;
use Faker\Generator as Faker;

$factory->define(SalesPacket::class, function (Faker $faker) {
    return [
        'sat' => 0,
        'converted' => $faker->dateTime,
        'site_plan' => $faker->dateTime,
        'pto' => $faker->dateTime,
        'design_plan_sent_date' => $faker->dateTime,
        'submitted_for_permitting_date' => $faker->dateTime,
        'permitting_received_date' => $faker->dateTime,
        'nem_doc_signed' => $faker->dateTime,
        'cpuc_doc_signed' => $faker->dateTime,
        'ach_doc_signed' => $faker->dateTime,
        'credit_doc_signed' => $faker->dateTime,
        'solar_agreement_signed' => $faker->dateTime,
        'proposal_doc_signed' => $faker->dateTime,
        'site_survey_note' => 'FILLED FROM FACTORY',
        'deleted_at' => null,
        'submitted' => null,
    ];
});
