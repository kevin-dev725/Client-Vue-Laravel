<?php

use App\State;
use Faker\Generator as Faker;

$factory->define(App\Lien::class, function (Faker $faker) {
    return [
        'state' => State::where('country_code', 'US')->inRandomOrder()->first(['iso_3166_2'])->iso_3166_2,
        'county' => $faker->city,
        'legal' => $faker->name,
        'lienor' => $faker->city,
        'owner' => $faker->name,
    ];
});
