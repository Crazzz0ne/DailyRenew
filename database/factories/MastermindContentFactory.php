<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Mastermind\MastermindContent::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis varius ac elit mollis sodales. 
        Aliquam erat volutpat. Sed sollicitudin at leo quis sodales. Sed dui lacus, molestie id ipsum vitae, aliquet 
        faucibus purus. Maecenas mollis egestas urna, a volutpat tellus viverra a. Vestibulum pulvinar nulla neque, vitae
         convallis eros euismod non. Nunc tellus risus, tincidunt id ultricies id',
        'category_id' => $faker->numberBetween(0, 6),
        'state' => 'CA',
        'user_id' => $faker->numberBetween(1, 2),
        'type' => $faker->randomElement($array = array('youTube', 'pdf')),
        'path' => 'eyuNrm4VK2w'
    ];
});
