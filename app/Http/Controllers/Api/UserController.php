<?php

namespace App\Http\Controllers\Api;

use App\Events\UserEvents\UserCreatedEvent;
use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\EloquentPhoneVerificationRepository;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Instanceof UserTransformer
     *
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * Instance of UserRepository
     *
     * @var UserRepository
     */
    private $userRepository;

    private $phoneVerificationRepository;

    public function __construct(UserRepository $userRepository, UserTransformer $userTransformer)
    {
        $this->userRepository  = $userRepository;
        $this->userTransformer = $userTransformer;

        $this->phoneVerificationRepository = new EloquentPhoneVerificationRepository(new PhoneVerification());

        parent::__construct();

    }

    public function AuthRouteAPI(Request $request){
        return $request->user();
     }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $users = $this->userRepository->findBy($request->all());

        return $this->respondWithCollection($users, $this->userTransformer);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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

        // Check if the phone number has been verified
        $isPhoneVerified = $this->phoneVerificationRepository->findOneBy([
            'phone'    => $request->input('phone'),
            'verified' => 1
        ]);

        if ( ! $isPhoneVerified instanceof PhoneVerification) {
            return $this->sendNotFoundResponse("The phone number has not been verified.");
        }

        $request->request->add(['phone_verified' => 1]);

        $request->request->add(['is_active' => 1]);

        $userInput = $this->getUserInput($request);

        $user = $this->userRepository->save($userInput);

        if ( ! $user instanceof User) {
            return $this->sendCustomResponse(500, 'Error occurred on creating User');
        }

        $settingInput = $this->getSettingInput($request);
        $imageInput   = $this->getImageInput($request);

        // fire user created event
        event(new UserCreatedEvent($user, $settingInput, $imageInput));

        return $this->setStatusCode(201)->respondWithItem($user, $this->userTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $email
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($email)
    {
        $user = $this->userRepository->findOneEmail($email);

        if ( ! $user instanceof User) {
            return $this->sendNotFoundResponse("The user with email {$email} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $user);

        return $this->respondWithItem($user, $this->userTransformer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request, $id));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user = $this->userRepository->findOne($id);

        if ( ! $user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $user);

        // User can modify only certain fields
        $newInput = $this->getUserInputUpdate($request);

        $user = $this->userRepository->update($user, $newInput);

        return $this->respondWithItem($user, $this->userTransformer);
    }

    /**
     * Remove the current user from the users table
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->destroyRequestValidationRules($request));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $currentUser = $this->getCurrentUserDetails();

        //check for soft or hard delete
        if ($request->delType == 'SOFT') {
            $currentUser->delete();

            return $this->sendCustomResponse("200", "The user with user id: {$currentUser->uid} is temporarily deleted.");
        }

        $userDirectoryPath = 'users/user_' . $currentUser->uid;

        //check for the existence of the path
        if (file_exists(storage_path('/app/public/' . $userDirectoryPath))) {
            $status = Storage::deleteDirectory('public/' . $userDirectoryPath);

            //check for the delete success
            if ($status) {
                $currentUser->forcedelete();

                return $this->sendCustomResponse("200", "The user with user id: {$currentUser->uid} is permanently deleted.");
            }
        } else {
            $currentUser->forcedelete();

            return $this->sendCustomResponse("200", "The user with user id: {$currentUser->uid} is permanently deleted.");
        }

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
            'name'          => 'required|max:100',
            'country_code'  => 'required|numeric',
            'phone'         => 'required|numeric|uniquePhoneField:' . $request->input('country_code'),
            'password'      => 'required|min:8|caseDiff|numbers',
            'gender'        => 'required|in:MALE,FEMALE',
            'dob'           => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            // Extra
            'about_me'      => 'max:255',
            'school'        => 'max:100',
            'work'          => 'max:255',

            //Image
            'image'         => 'required|imageBase64',
            'type'          => [
                'required',
                Rule::in(['image/png', 'image/jpg', 'image/jpeg'])
            ],
            'number'        => 'required|numeric|min:1|max:6',

            //Setting
            'interested_in' => 'required|in:FRIENDSHIP,RELATIONSHIP,CASUAL_MEETUP',
        ];

        $requestUser = $request->user();

        // Only admin user can set admin role.
        if ($requestUser instanceof User && $requestUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'in:BASIC_USER,ADMIN_USER';
        } else {
            $rules['role'] = 'in:BASIC_USER';
        }

        return $rules;
    }

    /**
     * Destroy user Request Validation Rules
     *
     * @param Request $request
     *
     * @return array
     *
     */
    private function destroyRequestValidationRules(Request $request)
    {
        $rules = [
            'delType' => 'required|in:SOFT,HARD'
        ];

        return $rules;
    }

    /**
     * Update Request validation Rules
     *
     * @param Request $request
     *
     * @return array
     */
    private function updateRequestValidationRules(Request $request, $id)
    {
        $rules = [
//            'phone'    => Rule::unique('users')->ignore($id, 'uid'),
            'name'     => 'required|max:100',
            'gender'   => 'required|in:MALE,FEMALE',
            'dob'      => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            // Extra
            'about_me' => 'max:255',
            'school'   => 'max:100',
            'work'     => 'max:255',

        ];

        $requestUser = $request->user();

        // Only admin user can set admin role.
        if ($requestUser instanceof User && $requestUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'in:BASIC_USER,ADMIN_USER';
        } else {
            $rules['role'] = 'in:BASIC_USER';
        }

        return $rules;
    }

    /**
     * Receive only user  input from request
     *
     * @param Request $request
     *
     * @return array
     */
    private function getUserInput(Request $request)
    {
        $userInput = $request->only([
            'name',
            'country_code',
            'phone',
            'password',
            'gender',
            'dob',
            'role',
            'phone_verified',
            'is_active'
        ]);

        return $userInput;
    }

    private function getUserInputUpdate(Request $request)
    {
        $userInput = $request->except([
            'id',
            'uid',
            'password',
            'country_code',
            'phone',
            'phone_verified',
            'email',
            'email_verified',
            'remember_token',
            'is_active',
            'provider',
            'provider_id',
            'access_token'
        ]);

        return $userInput;
    }

    /**
     * Receive only image input from request
     *
     * @param Request $request
     *
     * @return array
     */
    private function getImageInput(Request $request)
    {
        $imageInput = $request->only(['image', 'number', 'type']);

        return $imageInput;
    }

    /**
     * Receive only settings input from request
     *
     * @param Request $request
     *
     * @return array
     */
    private function getSettingInput(Request $request)
    {
        $settingInput = $request->only(['interested_in']);

        return $settingInput;
    }

    /**
     * Get details of current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetailUser()
    {
        $currentUser = $this->getCurrentUserDetails();
        $userId     = $currentUser->id;

        $user = $this->userRepository->findOneById($userId);

        return $this->respondWithItem($user, $this->userTransformer);
    }

    /**
     * Change password
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function changePassword(Request $request)
    {
        // Receive current user details
        $user = $this->getCurrentUserDetails();

        if ( ! $user instanceof User) {
            return $this->sendNotFoundResponse("There is something wrong on changing password. Please try again.");
        }

        if ($user->provider == 'facebook') {
            return $this->sendNotFoundResponse("Facebook users are not allowed to change password.");
        }

        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updatePasswordValidationRules());

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        if ( ! Hash::check($request->input('password_old'), $user->password)) {
            return $this->sendNotFoundResponse("Your old password is incorrect. Please try again.");
        }

        if ($request->input('password_old') == $request->input('password')) {
            return $this->sendNotFoundResponse("Your old password and new password is same. Please enter another password.");
        }

        $newPassword = [
            'password' => $request->input('password'),
        ];

        $user = $this->userRepository->update($user, $newPassword);

        $message = 'Your password is changed successfully.';

        return $this->sendCustomResponse('200', $message);
    }

    /**
     * Update password validation rules
     *
     * @return array
     */
    private function updatePasswordValidationRules()
    {
        $rules = [
            'password_old' => 'required',
            'password'     => 'required|min:8|caseDiff|numbers',
        ];

        return $rules;
    }

    /**
     * Remove the email
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function deleteEmail()
    {
        $currentUser = $this->getCurrentUserDetails();

        $user = $this->userRepository->findOneBy([
            'id' => $currentUser->id
        ]);

        if ($user->email == '') {
            return $this->sendNotFoundResponse("The user does not have any email.");
        }

        $input = [
            'email'          => null,
            'email_verified' => 0
        ];

        $this->userRepository->update($user, $input);

        return $this->sendCustomResponse("200", "The email {$currentUser->email} has been removed.");
    }
}

