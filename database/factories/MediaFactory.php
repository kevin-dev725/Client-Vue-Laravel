<?php

use Faker\Generator as Faker;

$factory->define(App\Media::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'file_name' => $faker->randomNumber() . '.png',
        'mime_type' => 'image/png',
        'size' => $faker->numberBetween(100, 20000),
        'manipulations' => json_encode([]),
        'custom_properties' => json_encode([
            'custom_headers' => [],
        ]),
        'responsive_images' => json_encode([]),
        'disk' => 'public',
    ];
});
