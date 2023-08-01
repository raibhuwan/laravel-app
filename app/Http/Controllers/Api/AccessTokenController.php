<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Http\Request;

class AccessTokenController extends Controller
{
    /**
     * Instance of UserRepository
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {

        $this->userRepository = $userRepository;

        parent::__construct();
    }

    /**
     * Since, with Laravel|Lumen passport doesn't restrict
     * a client requesting any scope. we have to restrict it.
     * http://stackoverflow.com/questions/39436509/laravel-passport-scopes
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function createAccessToken(Request $request)
    {

        $inputs = $request->all();

//        $user = null;
//        if (isset($inputs['username']) && $inputs['grant_type'] == 'password') {
//            $user = $this->userRepository->findOneBy(['email' => $inputs['username']]);
//        }
        //Set default scope with full access
//        if (!isset($inputs['scope']) || empty($inputs['scope'])) {
//            $inputs['scope'] = "users:write";
//        }

        if ( ! $request->has('scope')) {
            $request->request->add(['scope' => '*']);
        }

        $tokenRequest = $request->create('/oauth/token', 'post', $request->all());

        // forward the request to the oauth token request endpoint
        return \Route::dispatch($tokenRequest);
    }

    /**
     * Handles User Logout
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user('api')->token()->revoke();

        $message = 'The user has logged out successfully.';

        return $this->sendCustomResponse('200', $message);
    }
}
