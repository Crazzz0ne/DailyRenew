<?php


use Illuminate\Database\Seeder;
use App\Models\Mastermind\MastermindContent;

class MastermindContentTableSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        $vendor = factory(\App\Models\Mastermind\MastermindContent::class, 100)->create();

        $this->enableForeignKeys();
    }
}
