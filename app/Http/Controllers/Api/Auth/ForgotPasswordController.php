<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Jobs\Sms\SendForgotPasswordSmsJob;
use App\Mail\SendTestMail;
use App\Models\PasswordResetApi;
use App\Models\User;
use App\Repositories\EloquentPasswordResetApiRepository;
use App\Repositories\EloquentUserRepository;
use App\Transformers\PasswordResetApiTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset codes
    |
    */

    /**
     * @var PasswordResetApiTransformer
     */
    protected $passwordResetApiTransformer;

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
     * ForgotPasswordApiController constructor.
     */
    public function __construct()
    {
        $this->eloquentUserRepository             = new EloquentUserRepository(new User());
        $this->eloquentPasswordResetApiRepository = new EloquentPasswordResetApiRepository(new PasswordResetApi());
        $this->passwordResetApiTransformer        = new PasswordResetApiTransformer();

        parent::__construct();
    }

    /**
     * Send a reset code to the given user.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function sendResetCodePhone(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->validatePhone());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        };

        // Checks if the user exists
        $user_exists = $this->getUserByPhone($request->only('country_code', 'phone'));

        if ( ! $user_exists instanceof User) {
            return $this->sendNotFoundResponse("The user with phone {$request->input('country_code')}{$request->input('phone')} doesn't exist");
        }

        // Generate 6 digits pin
        $pin = generatePin();
        // Hash 6 digits pin
        $hashedPin = Hash::make($pin);

        $input = [
            'token'        => $hashedPin,
            'country_code' => $request->input('country_code'),
            'phone'        => $request->input('phone'),
            'expired_at'   => HelperFunctions::getExpiryTime()
        ];

        $password_reset_exists = $this->eloquentPasswordResetApiRepository->findOneBy([
                'country_code' => $request->input('country_code'),
                'phone'        => $request->input('phone')
            ]
        );

        if ( ! $password_reset_exists instanceof PasswordResetApi) {
            $password_reset_exists = $this->eloquentPasswordResetApiRepository->save($input);

        } else {
            $password_reset_exists = $this->eloquentPasswordResetApiRepository->update($password_reset_exists, $input);
        }

        if ( ! $password_reset_exists instanceof PasswordResetApi) {
            return $this->sendCustomResponse(500, 'Error occurred on creating reset token');
        }

        $message = $this->messageText($pin);

        // Send the code to the user using sms
        dispatch(new SendForgotPasswordSmsJob($password_reset_exists, $message));

        return $this->setStatusCode(201)->respondWithItem($password_reset_exists, $this->passwordResetApiTransformer);

    }

    /**
     * Verify phone validation rules
     * @return array
     *
     */
    private function validatePhone()
    {
        $rules = [
            'country_code' => 'required|numeric',
            'phone'        => 'required|numeric'
        ];

        return $rules;
    }

    private function getUserByPhone($user)
    {
        $user = $this->eloquentUserRepository->findOneBy([
            'country_code'   => $user['country_code'],
            'phone'          => $user['phone'],
            'phone_verified' => 1
        ]);

        return $user;
    }

    /**
     * Return text message
     *
     * @param $pin
     *
     * @return string
     */
    private function messageText($pin)
    {
        return trans('sms.forgotPassword.forgotPassword') . ' ' . trans('sms.pin.code') . ' ' . $pin . '. ' . trans('sms.pin.expires').' '.trans('sms.sender');
    }
}
