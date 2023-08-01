<?php

namespace App\Http\Controllers\Api;


use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Jobs\Sms\PhoneVerifiedSmsJob;
use App\Jobs\Sms\SendVerificationSmsJob;
use App\Mail\SendTestMail;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Repositories\EloquentPhoneVerificationRepository;
use App\Repositories\EloquentUserRepository;
use App\Transformers\PhoneVerificationTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PhoneVerificationController extends Controller
{
    /**
     * @var PhoneVerificationTransformer
     */
    protected $phoneVerificationTransformer;

    /**
     * @var EloquentPhoneVerificationRepository
     */
    protected $eloquentPhoneVerificationRepository;

    /**
     * @var EloquentUserRepository
     */
    protected $eloquentUserRepository;

    public function __construct()
    {
        $this->eloquentPhoneVerificationRepository = new EloquentPhoneVerificationRepository(new PhoneVerification());
        $this->phoneVerificationTransformer        = new PhoneVerificationTransformer();

        $this->eloquentUserRepository = new EloquentUserRepository(new User());

        parent::__construct();
    }

    /**
     *
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        // Generate 6 digits unique pin code
        $pin = generatePin();
        // Hash 6 digits pin
        $hashedPin = Hash::make($pin);

        $request->request->add(['token' => $hashedPin]);

        $after30minutes = HelperFunctions::getExpiryTime();

        $request->request->add(['expired_at' => $after30minutes]);
        $request->request->add(['verified' => 0]);

        $phoneExists = $this->eloquentPhoneVerificationRepository->findOneBy(['phone' => $request->input('phone')]);

        if ( ! $phoneExists instanceof PhoneVerification) {
            $verification = $this->eloquentPhoneVerificationRepository->save($request->all());
        } else {
            $verification = $this->eloquentPhoneVerificationRepository->update($phoneExists, $request->all());
        }

        if ( ! $verification instanceof PhoneVerification) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Verification code');
        }

        $message = $this->messageText($pin);

        dispatch(new SendVerificationSmsJob($verification, $message));

        return $this->setStatusCode(201)->respondWithItem($verification, $this->phoneVerificationTransformer);

    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     *
     * @return array
     */

    private function storeRequestValidationRules(Request $request)
    {
        $rules = [
            'country_code' => 'required|numeric',
            'phone'        => 'required|numeric|uniquePhoneField:' . $request->input('country_code'),
        ];

        return $rules;
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
        return trans('sms.pin.code') . ' ' . $pin . '. ' . trans('sms.pin.expires') . ' ' . trans('sms.sender');
    }

    /**
     * Verify phone validation rules
     * @return array
     *
     */
    public function verifyPhoneRules()
    {
        $rules = [
            'verification_code' => 'required|digits:6|numeric',
            'country_code'      => 'required|numeric',
            'phone'             => 'required|numeric',
        ];

        return $rules;
    }

    /**
     * Verifies the phone number after code is sent by user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function verifyPhone(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->verifyPhoneRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $input = [
            'country_code' => $request->input('country_code'),
            'phone'        => $request->input('phone')
        ];

        $phoneVerification = $this->eloquentPhoneVerificationRepository->findOneBy($input);

        if ( ! $phoneVerification instanceof PhoneVerification) {
            return $this->sendNotFoundResponse("There is something wrong. Please perform the phone verification again.");
        }

        if ( ! HelperFunctions::validateToken($request->input('verification_code'), $phoneVerification->token)) {
            return $this->sendNotFoundResponse("The verification code is invalid.");
        }

        if ( ! HelperFunctions::checkDateExpiration($phoneVerification->expired_at)) {
            return $this->sendNotFoundResponse("The verification code is expired");
        }

        $request->request->add(['verified' => 1]);

        $verification_details = $this->eloquentPhoneVerificationRepository->update($phoneVerification, $request->all());

        return $this->respondWithItem($verification_details, $this->phoneVerificationTransformer);

    }

    /**
     * After user registration the phone number in phone verification table should be deleted
     *
     * @param $phone
     */
    public function deletePhoneVerificationDetails($phone)
    {
        $phoneDetails = $this->eloquentPhoneVerificationRepository->findOneby(['phone' => $phone->phone]);

        if ( ! $phoneDetails instanceof PhoneVerification) {
            return;
        }
        $this->eloquentPhoneVerificationRepository->delete($phoneDetails);

        return;
    }

    /**
     * Change the phone number
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function changePhone(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->verifyPhoneRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user    = $this->getCurrentUserDetails();
        $user_id = $user->id;

//        if ($user->provider == 'facebook') {
//            return $this->sendNotFoundResponse("Facebook users are not allowed to change phone.");
//        }

        $input = [
            'country_code' => $request->input('country_code'),
            'phone'        => $request->input('phone'),
        ];

        $phoneVerification = $this->eloquentPhoneVerificationRepository->findOneBy($input);

        if ( ! $phoneVerification instanceof PhoneVerification) {
            return $this->sendNotFoundResponse("There is something wrong. Please perform the phone verification again.");
        }

        if ( ! HelperFunctions::validateToken($request->input('verification_code'), $phoneVerification->token)) {
            return $this->sendNotFoundResponse("The verification code is invalid.");
        }

        if ( ! HelperFunctions::checkDateExpiration($phoneVerification->expired_at)) {
            return $this->sendNotFoundResponse("The verification code is expired.");
        }

        $new_input = [
            'country_code'   => $phoneVerification->country_code,
            'phone'          => $phoneVerification->phone,
            'phone_verified' => 1
        ];

        $user = $this->eloquentUserRepository->findOneBy(['id' => $user_id]);

        $user = $this->eloquentUserRepository->update($user, $new_input);

        $this->deletePhoneVerificationDetails($user);

        $message = 'Your phone has been verified successfully.';

        if (config('smsNotification.phoneVerified')) {
            dispatch(new PhoneVerifiedSmsJob($user));
        }

        return $this->sendCustomResponse('200', $message);

    }
}
