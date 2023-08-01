<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'uid' => str_random(32),
            'name' => 'Simon Shrestha',
            'password' => \Illuminate\Support\Facades\Hash::make('Shrestha1'),

            'country_code' => '+977',
            'phone' => '984142938611',
            'phone_verified' => 1,
            'email' => 'cimon77@gmail.com',
            'email_verified' => 1,

            'gender' => 'male',
            'dob' => '1991-12-19',
            'role' => 'ADMIN_USER',
            'is_active' => 1
        ]);

        DB::table('users')->insert([
            'uid' => str_random(32),
            'name' => 'Bikram Pandit',
            'password' => \Illuminate\Support\Facades\Hash::make('Bikram123'),

            'country_code' => '+977',
            'phone' => '9804041788',
            'phone_verified' => 1,
            'email' => 'weirdocoder@gmail.com',
            'email_verified' => 1,

            'gender' => 'male',
            'dob' => '1991-12-19',
            'role' => 'ADMIN_USER',
            'is_active' => 1
        ]);

        DB::table('users')->insert([
            'uid' => str_random(32),
            'name' => 'Rachit Pokharel',
            'password' => \Illuminate\Support\Facades\Hash::make('Pokhrel1'),

            'country_code' => '+977',
            'phone' => '9860065930',
            'phone_verified' => 1,
            'email' => 'rachit.iosproshore@gmail.com',
            'email_verified' => 1,

            'gender' => 'male',
            'dob' => '1991-12-19',
            'role' => 'ADMIN_USER',
            'is_active' => 1
        ]);

    }
}
