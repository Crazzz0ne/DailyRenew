<?php


use App\Models\Collateral\CollateralContent;
use Illuminate\Database\Seeder;

class CollateralContentSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();
        $vendor = factory(CollateralContent::class, 100)->create();

        $this->enableForeignKeys();
    }

}
