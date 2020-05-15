<?php

use Faker\Generator as Faker;

$factory->define(App\ClientImport::class, function (Faker $faker) {
    return [
        'user_id' => factory(\App\User::class)->create()->id,
        'csv' => 'dev/imports/test-csv-import.txt',
        'status' => \App\ClientImport::STATUS_FINISHED,
    ];
});

$factory->state(App\ClientImport::class, 'error', function (Faker $faker) {
    return [
        'status' => \App\ClientImport::STATUS_ERROR,
        'errors' => ['Error importing csv file. This is probably due to invalid csv template.'],
        'exception' => [
            'message' => "The header record must be empty or a flat array with unique string values",
            'trace' => ['Line 1', 'Line 2'],
        ]
    ];
});
