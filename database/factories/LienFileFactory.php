<?php

use App\Lien;
use App\LienFile;
use Faker\Generator as Faker;

$factory->define(App\LienFile::class, function (Faker $faker) {
    return [
        'file_name' => $faker->uuid . '.png',
    ];
});
$factory->afterMaking(LienFile::class, function (LienFile $file) {
    if (!$file->lien_id) {
        $file->lien_id = factory(Lien::class)->create()->id;
    }
});
