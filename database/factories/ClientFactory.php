<?php

use App\Client;
use App\Country;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    $data = [
        'client_type' => Client::CLIENT_TYPE_INDIVIDUAL,
        'first_name' => $first_name = $faker->firstName,
        'middle_name' => $middle_name = $faker->lastName,
        'last_name' => $last_name = $faker->lastName,
        'phone_number' => $faker->e164PhoneNumber,
        'phone_number_ext' => '5555',
        'alt_phone_number' => (string) phone($faker->e164PhoneNumber,Country::getDefaultCountry()->iso_3166_2),
        'alt_phone_number_ext' => '4444',
        'street_address' => $faker->streetAddress,
        'street_address2' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->stateAbbr,
        'postal_code' => $faker->postcode,
        'email' => $faker->email,
		'country_id' => Country::getDefaultCountry()->id,
	];
    return $data;
});
$factory->state(Client::class, 'organization', function ($faker) {
    return [
        'client_type' => Client::CLIENT_TYPE_ORGANIZATION,
        'billing_first_name' => $faker->firstName,
        'billing_middle_name' => $faker->lastName,
        'billing_last_name' => $faker->lastName,
        'billing_phone_number' => (string) phone($faker->e164PhoneNumber,Country::getDefaultCountry()->iso_3166_2),
        'billing_phone_number_ext' => '5555',
        'billing_street_address' => $faker->streetAddress,
        'billing_street_address2' => $faker->streetAddress,
        'billing_city' => $faker->city,
        'billing_state' => $faker->stateAbbr,
        'billing_postal_code' => $faker->postcode,
        'billing_email' => $faker->email,
    ];
});
$factory->state(Client::class, 'complete', [
    'user_id' => function () {
        return factory(\App\User::class)->create()->id;
    },
]);
