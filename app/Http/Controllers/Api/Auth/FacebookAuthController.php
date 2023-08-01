<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\UserCreatedFacebookEvent;
use App\Helpers\FacebookHelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use App\Repositories\EloquentLocationRepository;
use App\Repositories\EloquentUserRepository;
use Carbon\Carbon;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;

class FacebookAuthController extends Controller
{

    /**
     * Facebook permissions
     *
     * @var array
     */
    private $facebookPermission = [
        'user_gender',
        'user_birthday',
        'user_photos',
        'public_profile'
    ];

    /**
     * @var EloquentUserRepository
     */
    protected $eloquentUserRepository;
    protected $eloquentLocationRepository;

    public function __construct()
    {
        $this->eloquentUserRepository     = new EloquentUserRepository(new User());
        $this->eloquentLocationRepository = new EloquentLocationRepository(new Location());
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
        $fb = FacebookHelperFunctions::facebook();

        try {
            // Returns a `FacebookFacebookResponse` object
            $response = $fb->get('/me?fields=id,name,gender,birthday,picture.width(720).height(720)',
                $request->input('fb_token'));
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $facebookUserDetails = $response->getDecodedBody();

        // Get list of permissions from token
        $permission = FacebookHelperFunctions::getPermissions($facebookUserDetails['id'], $request->input('fb_token'));

        // Check permission from facebook
        $this->checkAllPermission($permission, $this->facebookPermission);

        // check if account exist
        $input = [
            'provider_id' => $facebookUserDetails['id']
        ];

        $user = $this->eloquentUserRepository->findOneBy($input);


        if ($user instanceof User) {
            // if user already found
            $user_input = [
                'access_token' => $request->input('fb_token'),
            ];
            // update user
            $user = $this->eloquentUserRepository->update($user, $user_input);

        } else {
            $user_input = [
                'name'         => $facebookUserDetails['name'],
                'gender'       => $facebookUserDetails['gender'],
                'dob'          => Carbon::parse($facebookUserDetails['birthday'])->format('Y-m-d'),
                'role'         => 'BASIC_USER',
                'is_active'    => 1,
                'school'       => '',
                'work'         => '',
                'provider'     => 'facebook',
                'provider_id'  => $facebookUserDetails['id'],
                'access_token' => $request->input('fb_token'),
            ];
            // create a new user
            $user = $this->eloquentUserRepository->save($user_input);

            // fire user created event
            event(new UserCreatedFacebookEvent($user, $facebookUserDetails['picture']['data']['url']));
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

    /**
     * Check facebook permission
     *
     * @param $permissionFromFacebook
     * @param $facebookPermission
     *
     * @throws OAuthServerException
     */
    public function checkAllPermission($permissionFromFacebook, $facebookPermission)
    {
        $i                 = 0;
        $newPermissionList = [];
        foreach ($permissionFromFacebook as $key) {
            $newPermissionList[$i] = $key['permission'];
            $i++;
        }

        $result = array_diff($facebookPermission, $newPermissionList);

        $permissionText = '';

        // Check if the client has requested all the necessary permission in facebook
        if ( ! empty($result)) {
            foreach ($result as $key) {
                $permissionText .= $key . ', ';
            }

            throw OAuthServerException::serverError(rtrim($permissionText,
                    ', ') . ' permissions has not been requested.');
        }

        // Check if the user has given all permissions from facebook
        $permissionText = '';
        $flag           = 0;
        foreach ($permissionFromFacebook as $key) {
            if ($key['status'] == 'declined') {
                $permissionText .= $key['permission'] . ', ';
                $flag           = 1;
            }
        }

        if ($flag == 1) {
            throw OAuthServerException::serverError(rtrim($permissionText,
                    ', ') . ' permissions has been declined by user.');
        }

    }
}
