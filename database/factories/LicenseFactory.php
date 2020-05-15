<?php

use App\License;
use App\Media;
use App\User;
use Faker\Generator as Faker;

$factory->define(App\License::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'number' => $faker->uuid,
        'expiration' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
        'is_insured' => $faker->boolean,
    ];
});

$factory->afterMaking(License::class, function (License $license) {
    if (!$license->user_id) {
        $license->user_id = factory(User::class)->create()->id;
    }
});

$factory->afterCreatingState(License::class, 'with photos', function (License $license) {
    factory(Media::class, 2)
        ->create([
            'model_type' => License::class,
            'model_id' => $license->id,
            'collection_name' => License::MEDIA_COLLECTION_PHOTOS,
        ]);
});

$factory->afterCreatingState(License::class, 'with certs', function (License $license) {
    factory(Media::class, 3)
        ->create([
            'model_type' => License::class,
            'model_id' => $license->id,
            'collection_name' => License::MEDIA_COLLECTION_CERTS,
        ]);
});
