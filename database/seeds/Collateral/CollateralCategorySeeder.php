<?php


use App\Models\Collateral\CollateralCategory;

use Illuminate\Database\Seeder;

class CollateralCategorySeeder extends Seeder
{
    public function run()
    {
        $vendor = factory(CollateralCategory::class, 6)->create();
    }
}
