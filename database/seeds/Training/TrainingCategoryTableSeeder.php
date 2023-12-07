<?php


use App\Models\Training\TrainingCategory;
use Illuminate\Database\Seeder;

class TrainingCategoryTableSeeder extends Seeder
{
    public function run()
    {
        $vendor = factory(TrainingCategory::class, 6)->create();
    }
}
