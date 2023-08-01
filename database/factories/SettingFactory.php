<?php
use App\Models\Setting;
use App\Models\User;

$factory->define(Setting::class, function (Faker\Generator $faker) {

    $minAge = $faker->numberBetween($min = 18, $max = 45);

    return [
        'uid'                   => Ramsey\Uuid\Uuid::uuid4(),
        'search_distance'       => $faker->numberBetween($min = 1, $max = 100),
        'distance_in'           => $faker->randomElement(['MI', 'KM']),
        'show_ages_min'         => $minAge,
        'show_ages_max'         => $faker->numberBetween($min = $minAge, $max = 55),
        'interested_in'         => $faker->randomElement(['FRIENDSHIP', 'RELATIONSHIP', 'CASUAL_MEETUP']),
        'date_with'             => $faker->randomElement(['MALE', 'FEMALE', 'BOTH']),
        'privacy_show_distance' => $faker->numberBetween($min = 0, $max = 1),
        'privacy_show_age'      => $faker->numberBetween($min = 0, $max = 1),
    ];
});