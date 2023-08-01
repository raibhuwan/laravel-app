<?php

use App\Repositories\Contracts\ChatMessageRepository;
use App\Repositories\Contracts\SwipeMatchRepository;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Repositories\Contracts\ChatMessageRecipientRepository;
use Ramsey\Uuid\Uuid;
use App\Repositories\Contracts\UserRepository;
use App\Models\User;

class DemoUsersTableSeeder extends Seeder
{
    private $swipeMatchRepository;
    private $chatMessageRepository;
    private $chatMessageRecipientRepository;
    private $userRepository;

    public function __construct(
        SwipeMatchRepository $swipeMatchRepository,
        ChatMessageRepository $chatMessageRepository,
        ChatMessageRecipientRepository $chatMessageRecipientRepository,
        UserRepository $userRepository
    ) {
        $this->swipeMatchRepository           = $swipeMatchRepository;
        $this->chatMessageRepository          = $chatMessageRepository;
        $this->chatMessageRecipientRepository = $chatMessageRecipientRepository;
        $this->userRepository                 = $userRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userAndrewExist  = $this->userRepository->findOneBy(['email' => 'andrew@email.com']);
        $userJessicaExist = $this->userRepository->findOneBy(['email' => 'jessica@email.com']);

        if ( ! $userAndrewExist instanceof User && ! $userJessicaExist instanceof User) {
            $user = factory(App\Models\User::class, 1)->create([
                'name'         => 'Andrew Casey',
                'country_code' => '+1',
                'phone'        => '123456789',
                'email'        => 'andrew@email.com',
                'password'     => bcrypt('Password1'),
                'gender'       => 'MALE',
                'dob'          => Carbon::now()->subYears(26)
            ])->each(function ($user) {
                factory(App\Models\Setting::class)->create([
                    'user_id'         => $user->id,
                    'search_distance' => '35',
                    'show_ages_min'   => '18',
                    'show_ages_max'   => '37',
                    'interested_in'   => 'RELATIONSHIP',
                    'date_with'       => 'FEMALE'
                ]);
                factory(App\Models\Image::class)->create([
                    'user_id' => $user->id,
                    'number'  => 1,
                    'path'    => 'https://i.ibb.co/nR42TY0/model-2911325-1920.jpg'
                ]);
                factory(App\Models\Image::class)->create([
                    'user_id' => $user->id,
                    'number'  => 2,
                    'path'    => 'https://i.ibb.co/ZN5w4Wt/model-2911332-1920.jpg'
                ]);
                factory(App\Models\Image::class)->create([
                    'user_id' => $user->id,
                    'number'  => 3,
                    'path'    => 'https://i.ibb.co/7zgn35z/model-2911330-1920.jpg'
                ]);
                factory(App\Models\Location::class)->create(['user_id' => $user->id]);
            });

            $user2 = factory(App\Models\User::class, 1)->create([
                'name'         => 'Jessica Charles',
                'country_code' => '+1',
                'phone'        => '987654321',
                'email'        => 'jessica@email.com',
                'password'     => bcrypt('Password2'),
                'gender'       => 'FEMALE',
                'dob'          => Carbon::now()->subYears(18)->subDay(1)
            ])->each(function ($user2) {
                factory(App\Models\Setting::class)->create([
                    'user_id'         => $user2->id,
                    'search_distance' => '35',
                    'show_ages_min'   => '18',
                    'show_ages_max'   => '37',
                    'interested_in'   => 'RELATIONSHIP',
                    'date_with'       => 'MALE'
                ]);
                factory(App\Models\Image::class)->create([
                    'user_id' => $user2->id,
                    'number'  => 1,
                    'path'    => 'https://i.ibb.co/Mh14NTF/woman-1948939-1280.jpg'
                ]);
                factory(App\Models\Image::class)->create([
                    'user_id' => $user2->id,
                    'number'  => 2,
                    'path'    => 'https://i.ibb.co/r2zZ2bd/hair-1462984-1920.jpg'
                ]);
                factory(App\Models\Image::class)->create([
                    'user_id' => $user2->id,
                    'number'  => 3,
                    'path'    => 'https://i.ibb.co/4FnPC6W/woman-1466631-1920.jpg'
                ]);
                factory(App\Models\Location::class)->create(['user_id' => $user2->id]);

            });


            $input = [
                'a' => $user[0]['id'],
                'b' => $user2[0]['id']
            ];

            $this->swipeMatchRepository->save($input);

            // Insert demo messages
            $inputMessage = [
                'creator_id'   => $user[0]['id'],
                'message_body' => 'Hi Jessica!'
            ];

            $message = $this->chatMessageRepository->save($inputMessage);

            $inputMessageRecipient = [
                'recipient_id'      => $user2[0]['id'],
                'chat_message_id'   => $message->id,
                'device_message_id' => 'n-' . Uuid::uuid4()

            ];

            $messageRecipient = $this->chatMessageRecipientRepository->save($inputMessageRecipient);

            $inputMessage2 = [
                'creator_id'   => $user2[0]['id'],
                'message_body' => 'Hey'
            ];

            $message2 = $this->chatMessageRepository->save($inputMessage2);

            $inputMessageRecipient2 = [
                'recipient_id'      => $user[0]['id'],
                'chat_message_id'   => $message2->id,
                'device_message_id' => 'n-' . Uuid::uuid4()
            ];

            $messageRecipient = $this->chatMessageRecipientRepository->save($inputMessageRecipient2);
        }

        $userAdminExist = $this->userRepository->findOneBy(['email' => 'admin@email.com']);

        if(!$userAdminExist instanceof User) {
            $userAdmin = factory(App\Models\User::class, 1)->create([
                'name'         => 'Admin user',
                'country_code' => '+1',
                'phone'        => '5555599999',
                'email'        => 'admin@email.com',
                'password'     => bcrypt('Password3'),
                'gender'       => 'MALE',
                'dob'          => Carbon::now()->subYears(18)->subDay(1),
                'role'         => 'ADMIN_USER'
            ])->each(function ($user2) {
                factory(App\Models\Setting::class)->create([
                    'user_id'         => $user2->id,
                    'search_distance' => '35',
                    'show_ages_min'   => '18',
                    'show_ages_max'   => '37',
                    'interested_in'   => 'RELATIONSHIP',
                    'date_with'       => 'FEMALE'
                ]);
                factory(App\Models\Image::class)->create([
                    'user_id' => $user2->id,
                    'number'  => 1,
                    'path'    => 'https://i.ibb.co/9Ywkgwx/male-call-center-agent-1270-395.jpg'
                ]);

                factory(App\Models\Location::class)->create(['user_id' => $user2->id]);

            });
        }

    }
}
