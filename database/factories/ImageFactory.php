<?php
use App\Models\Image;

$factory->define(Image::class, function (Faker\Generator $faker) {
    return [
        'uid'                   => Ramsey\Uuid\Uuid::uuid4(),
        'name'                  => $faker->numberBetween($min = 1, $max = 100),
        'link'                  => 1
    ];
});

