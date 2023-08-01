<?php

namespace App\Http\Controllers\Api;

use App\Helpers\HelperFunctions;
use App\Http\Controllers\Controller;
use App\Jobs\Email\SendEmailVerifiedJob;
use App\Jobs\Email\SendVerificationEmailJob;
use App\Models\EmailVerification;
use App\Models\User;
use App\Repositories\EloquentEmailVerificationRepository;
use App\Repositories\EloquentUserRepository;
use App\Transformers\EmailVerificationTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController extends Controller
{
    /**
     * @var EmailVerificationTransformer
     */
    protected $emailVerificationTransformer;

    /**
     * @var EloquentEmailVerificationRepository
     */
    protected $eloquentEmailVerificationRepository;

    /**
     * @var EloquentUserRepository
     */
    protected $eloquentUserRepository;

    public function __construct()
    {
        $this->eloquentEmailVerificationRepository = new EloquentEmailVerificationRepository(new EmailVerification());
        $this->emailVerificationTransformer        = new EmailVerificationTransformer();

        $this->eloquentUserRepository = new EloquentUserRepository(new User());

        parent::__construct();

    }

    /**
     * Registers new validation code and send it to the user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(Request $request)
    {

        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }
        // Generate 6 digits unique pin code
        $pin = generatePin();
        // Hash 6 digits pin
        $hashedPin = Hash::make($pin);

        $request->request->add(['token' => $hashedPin]);

        $currentUser = $this->getCurrentUserDetails();
        $userId     = $currentUser->id;

        $request->request->add(['user_id' => $userId]);
        $request->request->add(['expired_at' => HelperFunctions::getExpiryTime()]);
        $request->request->add(['verified' => 0]);

        $email_exists = $this->eloquentEmailVerificationRepository->findOneBy(['email' => $request->input('email')]);

        if ( ! $email_exists instanceof EmailVerification) {
            $verification = $this->eloquentEmailVerificationRepository->save($request->all());
        } else {
            $verification = $this->eloquentEmailVerificationRepository->update($email_exists, $request->all());
        }

        if ( ! $verification instanceof EmailVerification) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Verification code');
        }

        dispatch(new SendVerificationEmailJob($verification, $pin));

//        Mail::to($verification->email)->send(new EmailVerificationMail($verification));

        return $this->setStatusCode(201)->respondWithItem($verification, $this->emailVerificationTransformer);

    }

    /**
     * Store Request Validation Rules
     *
     * @return array
     */

    private function storeRequestValidationRules()
    {
        $rules = [
            'email' => 'email|required|unique:users',
        ];

        return $rules;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function verifyEmail(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->verifyEmailRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $currentUser       = $this->getCurrentUserDetails();
        $userId           = $currentUser->id;
        $verification_code = $request->input('verification_code');

        $input = [
            'user_id' => $userId,
            'email'   => $request->input('email')
        ];

        $emailVerification = $this->eloquentEmailVerificationRepository->findOneBy($input);

        if ( ! $emailVerification instanceof EmailVerification) {
            return $this->sendNotFoundResponse("There is something wrong. Please perform the email verification again.");
        }

        if ( ! HelperFunctions::validateToken($request->input('verification_code'), $emailVerification->token)) {
            return $this->sendNotFoundResponse("The verification code is invalid.");
        }

        if ( ! HelperFunctions::checkDateExpiration($emailVerification->expired_at)) {
            return $this->sendNotFoundResponse("The verification code is expired");
        }


        $newInput = [
            'email'          => $emailVerification->email,
            'email_verified' => 1
        ];

        $user = $this->eloquentUserRepository->findOneBy(['id' => $userId]);

        $user = $this->eloquentUserRepository->update($user, $newInput);

        $this->deleteEmailVerificationDetails($user);

        $message = 'Your email has been verified successfully.';

        dispatch(new SendEmailVerifiedJob($user));

        return $this->sendCustomResponse('200', $message);

    }

    /**
     * Verify email validation rules
     * @return array
     *
     */
    public function verifyEmailRules()
    {
        $rules = [
            'verification_code' => 'required|digits:6|numeric',
            'email'             => 'required|email'
        ];

        return $rules;
    }

    /**
     * After user verifies the email, the record in email verification table should be deleted
     *
     * @param $email
     */
    public function deleteEmailVerificationDetails($email)
    {
        $emailDetails = $this->eloquentEmailVerificationRepository->findOneBy(['email' => $email->email]);

        if ( ! $emailDetails instanceof EmailVerification) {
            return;
        }
        $this->eloquentEmailVerificationRepository->delete($emailDetails);

        return;
    }
}