<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use Faker\Generator as Faker;
use App\Models\Training;


$factory->define(App\Models\Training\TrainingContent::class, function (Faker $faker) {
    $google = function ($min, $max, $weightedMax) {
        $arr = array();
        for ($i = 0; $i < 10; $i++) {
            $arr[] = rand($min, $weightedMax);
        }
        $arr[] = rand($min, $max);
        return $arr;
    };

    return [
        'name' => $faker->name,
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis varius ac elit mollis sodales. 
        Aliquam erat volutpat. Sed sollicitudin at leo quis sodales. Sed dui lacus, molestie id ipsum vitae, aliquet 
        faucibus purus. Maecenas mollis egestas urna, a volutpat tellus viverra a. Vestibulum pulvinar nulla neque, vitae
         convallis eros euismod non. Nunc tellus risus, tincidunt id ultricies id',
        'category_id' => $faker->numberBetween(0, 6),
        'state' => 'CA',
        'user_id' => $faker->numberBetween(1, 2),
        'vendor_id' => $faker->biasedNumberBetween(1, 6, ['\Faker\Provider\Biased', 'linearLow']),
        'type' => $faker->randomElement($array = array('youTube', 'pdf', 'audio')),
        'path' => 'eyuNrm4VK2w'
    ];
});
