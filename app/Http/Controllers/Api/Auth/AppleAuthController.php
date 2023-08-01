<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Location;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\EloquentImageRepository;
use App\Repositories\EloquentLocationRepository;
use App\Repositories\EloquentSettingRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\Exception\OAuthServerException;

class AppleAuthController extends Controller
{

    /**
     * @var EloquentUserRepository
     */
    protected $eloquentUserRepository;
    protected $eloquentLocationRepository;
    /**
     * @var EloquentSettingRepository
     */
    protected $eloquentSettingRepository;
    /**
     * @var EloquentImageRepository
     */
    protected $eloquentImageRepository;

    public function __construct()
    {
        $this->eloquentUserRepository     = new EloquentUserRepository(new User());
        $this->eloquentLocationRepository = new EloquentLocationRepository(new Location());
        $this->eloquentSettingRepository  = new EloquentSettingRepository(new Setting());
        $this->eloquentImageRepository    = new EloquentImageRepository(new Image());

        parent::__construct();
    }

    /**
     * Login or create account during facebook login
     *
     * @param Request $request
     *
     * @return $this|\Illuminate\Database\Eloquent\Model|mixed|null|static
     */
    public function loginOrCreateAccount(Request $request)
    {
        $token      = $request->input('apple_token');
        $socialUser = Socialite::driver('apple')->userFromToken($token);

        // check if account exist
        $input = [
            'provider_id' => $socialUser->id,
            'provider'    => 'apple'
        ];

        $user = $this->eloquentUserRepository->findOneBy($input);

        if ($user instanceof User) {
            // if user already found
            $user_input = [
                'access_token' => $token,
            ];
            // update user
            $user = $this->eloquentUserRepository->update($user, $user_input);

        } else {

            $user = $this->eloquentUserRepository->findOneByWithTrashed($input);

            if ($user instanceof User) {
                die('Your account has been temporarily deleted.');
            }

            $user_input = [
                'name'           => !empty($socialUser->name) ? $socialUser->name : uniqid('appleuser'),
                'gender'         => 'MALE',
                'role'           => 'BASIC_USER',
                'email'          => $socialUser->email,
                'email_verified' => 1,
                'is_active'      => 1,
                'school'         => '',
                'work'           => '',
                'provider'       => 'apple',
                'provider_id'    => $socialUser->id,
                'access_token'   => $token,
            ];

            // create a new user
            $user = $this->eloquentUserRepository->save($user_input);

            $input['user_id']               = $user->id;
            $input['search_distance']       = config('default.setting.search_distance');
            $input['distance_in']           = config('default.setting.distance_in');
            $input['show_ages_min']         = config('default.setting.show_ages_min');
            $input['show_ages_max']         = config('default.setting.show_ages_max');
            $input['interested_in']         = config('default.setting.interested_in');
            $input['date_with']             = (isset($user) && strtoupper($user['gender']) == 'MALE') ? 'FEMALE' : 'MALE';
            $input['privacy_show_distance'] = 1;
            $input['privacy_show_age']      = 1;

            $this->eloquentSettingRepository->save($input);

            $input = [
                'user_id' => $user->id,
                'name'    => $user->id,
                'number'  => 1,
                'path'    => 'https://i.ibb.co/0Dm5jHF/placeholder.jpg',
                'link'    => 1
            ];

            $this->eloquentImageRepository->save($input);
        }

        $locationExists = $this->eloquentLocationRepository->findOneBy([
            'user_id' => $user->id
        ]);

        $input = [
            'user_id'   => $user->id,
            'latitude'  => 0.0000,
            'longitude' => 0.0000
        ];

        if ($locationExists instanceof Location) {
            $location = $this->eloquentLocationRepository->update($locationExists, $input);
        } else {
            $location = $this->eloquentLocationRepository->save($input);
        }

        return $user;
    }
}
