<?php
use App\Models\User;

$factory->define(User::class, function (Faker\Generator $faker) {

    $gender = $faker->randomElement(['MALE','FEMALE']);

    return [
        'uid'            => Ramsey\Uuid\Uuid::uuid4(),
        'name'           => $faker->name(strtolower($gender)),
        'password'       => bcrypt('password'),
        'country_code'   => '+' . $faker->areaCode,
        'phone'          => $faker->numberBetween($min = 1000000000, $max = 9999999999),
        'phone_verified' => 1,
        'email'          => $faker->email,
        'email_verified' => 1,
        'gender'         => $gender,
        'dob'            => $faker->dateTimeBetween($startDate = '-37 years', $endDate = '-18 years'),
        'role'           => 'BASIC_USER',
        'about_me'       => $faker->realText($maxNbChars = 150, $indexSize = 2),
        'school'         => $faker->company,
        'work'           => $faker->company,
        'is_active'      => 1,
        'is_fake'        => 1
        ];
});