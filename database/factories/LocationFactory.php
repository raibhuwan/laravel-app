<?php
use App\Models\Location;

$factory->define(Location::class, function (Faker\Generator $faker) {

    $contents = storage_path() . '/'. config('storage.location_file_name');

    $locations = json_decode(file_get_contents($contents), true);

    $latitude = $locations[array_rand($locations)]['latitude'];
    $longitude = $locations[array_rand($locations)]['longitude'];

    return [
        'uid'       => Ramsey\Uuid\Uuid::uuid4(),
        'latitude'  => $latitude,
        'longitude' => $longitude
    ];
});