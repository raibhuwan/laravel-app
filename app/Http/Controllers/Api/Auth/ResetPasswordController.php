<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\PasswordResetEvent;
use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Jobs\Sms\SendPasswordChangedSmsJob;
use App\Models\PasswordResetApi;
use App\Models\User;
use App\Repositories\EloquentPasswordResetApiRepository;
use App\Repositories\EloquentUserRepository;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    |
    */

    /**
     * @var EloquentUserRepository
     */
    protected $eloquentUserRepository;

    /**
     * @var EloquentPasswordResetApiRepository
     */
    protected $eloquentPasswordResetApiRepository;

    /**
     * Create a new controller instance.
     *
     * ResetPasswordController constructor.
     */
    public function __construct()
    {
        $this->eloquentUserRepository = new EloquentUserRepository(new User());
        $this->eloquentPasswordResetApiRepository = new EloquentPasswordResetApiRepository(new PasswordResetApi());

        parent::__construct();
    }

    /**
     * Reset the given user's password.
     *
     * @param Request $request
     *
     * @return mixed|string
     */
    public function reset(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->resetPasswordPhoneValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        };

        // Checks if the user exists
        $user_exists = $this->getUserByPhone($request->only('country_code', 'phone'));

        if (!$user_exists instanceof User) {
            return $this->sendNotFoundResponse("The user with phone {$request->input('country_code')}{$request->input('phone')} doesn't exist");
        }

        $input = [
            'country_code' => $request->input('country_code'),
            'phone' => $request->input('phone'),
        ];

        $resetPassword = $this->eloquentPasswordResetApiRepository->findOneBy($input);

        if (!$resetPassword instanceof PasswordResetApi) {
            return $this->sendNotFoundResponse("There is something wrong. Please perform the reset password again.");
        }

        if (!HelperFunctions::validateToken($request->input('verification_code'), $resetPassword->token)) {
            return $this->sendNotFoundResponse("The verification code is invalid.");
        }

        if (!HelperFunctions::checkDateExpiration($resetPassword->expired_at)) {
            return $this->sendNotFoundResponse("The verification code is expired.");
        }

        $new_password = [
            'password' => $request->input('password')
        ];

        $user = $this->eloquentUserRepository->update($user_exists, $new_password);

        //to switch the sms on or off from env variable
        if (config('smsNotification.passwordChangedSuccessfully')) {
            dispatch(new SendPasswordChangedSmsJob($user));
        }

        $this->eloquentPasswordResetApiRepository->delete($resetPassword);

        // fire user created event
        event(new PasswordResetEvent($user));

        $message = "Your password has been reset.";

        return $this->sendCustomResponse('200', $message);
    }

    private function resetPasswordPhoneValidationRules()
    {
        $rules = [
            'country_code' => 'required|numeric',
            'phone' => 'required|numeric',
            'verification_code' => 'required|digits:6|numeric',
            'password' => 'required|min:8|caseDiff|numbers',
        ];

        return $rules;
    }

    /**
     * Get user by phone
     *
     * @param $user
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed|null|static
     */
    private function getUserByPhone($user)
    {
        $user = $this->eloquentUserRepository->findOneBy([
            'country_code' => $user['country_code'],
            'phone' => $user['phone'],
            'phone_verified' => 1
        ]);

        return $user;
    }
}
