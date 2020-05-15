<?php

use App\FlaggedPhrase;
use Faker\Generator as Faker;

$factory->define(FlaggedPhrase::class, function (Faker $faker) {
    return [
        'phrase' => join(' ', $faker->unique()->words),
    ];
});
