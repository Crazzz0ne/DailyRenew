<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Epc\Epc;
use App\Models\Office\Office;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\SalesPacket;
use Faker\Generator as Faker;

$factory->define(Lead::class, function (Faker $faker) {
    return [
        'status_id' => 1,
        'office_id' => null,
        'epc_id' => null,
        'credit_status_id' => 1,
        'integrations_approved' => 3,
        'origin_office_id' => 1,
        'customer_id' => 1,
        'sales_packet_id' => 1,
        'utility_id' => 1,
    ];
});

