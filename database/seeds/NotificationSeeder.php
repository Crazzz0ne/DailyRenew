<?php

use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Notifications\Notification::class, 12)->create([
            'notifiable_id' => 1
        ]);

        factory(\App\Models\Notifications\Notification::class, 50)->create();
    }
}
