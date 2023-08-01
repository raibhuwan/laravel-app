<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//         $this->call(UsersTableSeeder::class);
        factory(App\Models\User::class, 10)->create()->each(function ($user) {
            factory(App\Models\Setting::class)->create(['user_id' => $user->id]);

            if ($user->gender == 'MALE') {
                $contents      = storage_path() . '/' . config('storage.male_image_link');
                $maleImageLink = json_decode(file_get_contents($contents), true);
                $imageUrl      = $maleImageLink[array_rand($maleImageLink)]['image_url'];
            } else {
                $contents        = storage_path() . '/' . config('storage.female_image_link');
                $femaleImageLink = json_decode(file_get_contents($contents), true);
                $imageUrl        = $femaleImageLink[array_rand($femaleImageLink)]['image_url'];
            }

            factory(App\Models\Image::class)->create(['user_id' => $user->id, 'number' => 1, 'path' => $imageUrl]);
            factory(App\Models\Location::class)->create(['user_id' => $user->id]);
        });
//        $this->call(PlansTableSeeder::class);
    }
}
