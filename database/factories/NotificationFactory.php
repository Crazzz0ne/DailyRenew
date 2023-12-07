<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Auth\User;
use Faker\Generator as Faker;

$factory->define(\App\Models\Notifications\Notification::class, function (Faker $faker) {
    $batch = factory(\App\Models\Notifications\NotificationBatch::class, 1)->create()->first();
    $users = User::all();
    $leads = \App\Models\SalesFlow\Lead\Lead::all();
    $uuid = $faker->unique()->uuid;

    $obj = new stdClass;
    $obj->notification_id = $uuid;
    $obj->batch_id = $batch->id;
    $obj->url = "http://scout.test/dashboard/lead/{$leads->random()->id}";
    $obj->body = "Seeded Notification";
    $obj->read_at = rand(0, 1) ? $faker->dateTime : null;
    $obj->time = $faker->dateTime;

    return [
        'id' => $uuid,
        'type' => 'App\Notifications\Frontend\Lead\NewLeadNoteUserNotification',
        'notifiable_type' => 'App\Models\Auth\User',
        'notifiable_id' => $users->random()->id,
        'data' => $obj,
        'batch_id' => $batch->id,
        'read_at' => rand(0, 1) ? $faker->dateTime : null,
    ];
});
