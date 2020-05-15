<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'role_id' => \App\Role::ROLE_USER,
        'account_type' => \App\User::ACCOUNT_TYPE_INDIVIDUAL,
        'phone_number' => $faker->phoneNumber,
        'street_address' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => 'AU',
        'postal_code' => $faker->postcode,
    ];
});

$factory->state(App\User::class, 'admin', function (Faker $faker) {
    return [
        'role_id' => \App\Role::ROLE_ADMIN,
    ];
});
