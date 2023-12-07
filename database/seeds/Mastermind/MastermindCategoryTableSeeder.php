<?php


use App\Models\Mastermind\MastermindCategory;
use Illuminate\Database\Seeder;

class MastermindCategoryTableSeeder extends Seeder
{
    public function run()
    {
        $vendor = factory(MastermindCategory::class, 6)->create();
    }
}
