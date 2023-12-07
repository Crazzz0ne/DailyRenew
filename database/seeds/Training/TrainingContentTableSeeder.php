<?php


use App\Models\Training\TrainingContent;
use Illuminate\Database\Seeder;

class TrainingContentTableSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        $vendor = factory(TrainingContent::class, 100)->create();

        $this->enableForeignKeys();
    }
}
