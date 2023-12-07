<?php

use App\RoofType;
use Illuminate\Database\Seeder;

class RoofTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RoofType::class, 1)->create([
            "name" => "Comp shingle"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Concrete Tile"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Tar & Gravel"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Clay Tile"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Light Weight Concrete Tile"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Flat Roof"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Torch down"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Foam roof"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Corrugated Metal"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Standing Seam Metal"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Woodshake"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Calshake"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Slate"
        ]);
        factory(RoofType::class, 1)->create([
            "name" => "Stone Coated Metal"
        ]);
    }
}
