<?php

use App\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $announcement = factory(\App\Models\Announcement\Announcement::class, 5)->create();
    }
}
