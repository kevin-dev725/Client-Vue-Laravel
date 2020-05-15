<?php

use App\Review;
use Faker\Generator as Faker;

$factory->define(Review::class, function (Faker $faker) {
    return [
        'service_date' => today(),
        'star_rating' => $faker->numberBetween(1, 5),
        'payment_rating' => Review::REVIEW_RATING_THUMBS_UP,
        'character_rating' => Review::REVIEW_RATING_THUMBS_DOWN,
        'repeat_rating' => Review::REVIEW_RATING_THUMBS_UP,
        'comment' => $faker->paragraph,
    ];
});
$factory->state(Review::class, 'complete', [
    'user_id' => function () {
        return factory(\App\User::class)->create()->id;
    },
    'client_id' => function (array $review) {
        return factory(\App\Client::class)->create([
            'user_id' => $review['user_id']
        ])->id;
    }
]);
