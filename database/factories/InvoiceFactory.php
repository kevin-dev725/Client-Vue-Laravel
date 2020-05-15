<?php

use Faker\Generator as Faker;

$factory->define(App\Invoice::class, function (Faker $faker) {
    return [
        'stripe_id' => 'in_' . str_random(),
        'amount' => $faker->numberBetween(20, 100)
    ];
});
$factory->state(\App\Invoice::class, 'complete', function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class)->create()->id,
    ];
});
