<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\EloquentSettingRepository;
use App\Repositories\EloquentUserRepository;
use App\Models\Setting;
use App\Transformers\SettingTransformer;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * @var EloquentSettingRepository
     */
    protected $eloquentSettingRepository;

    /**
     * @var SettingTransformer
     */
    protected $settingTransformer;

    /**
     * @var EloquentUserRepository
     */
    protected $eloquentUserRepository;

    public function __construct()
    {
        $this->eloquentSettingRepository = new EloquentSettingRepository(new Setting());
        $this->settingTransformer        = new SettingTransformer();
        $this->eloquentUserRepository    = new EloquentUserRepository(new User());

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $setting = $this->eloquentSettingRepository->findBy($request->all());

        return $this->respondWithCollection($setting, $this->settingTransformer);
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
        $currentUser = $this->getCurrentUserDetails();

        $settingExists = $this->eloquentSettingRepository->findOneBy([
            'user_id' => $currentUser->id
        ]);

        if ($settingExists instanceof Setting) {
            return $this->sendNotFoundResponse("The setting for user id: {$currentUser->uid} already exists.");
        }

        $setting = $this->eloquentSettingRepository->save($request->all());

        if ( ! $setting instanceof Setting) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Setting');
        }

        return $this->setStatusCode(201)->respondWithItem($setting, $this->settingTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id)
    {
        $setting = $this->eloquentSettingRepository->findOne($id);

        if ( ! $setting instanceof Setting) {
            return $this->sendNotFoundResponse("The setting with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $setting);

        return $this->respondWithItem($setting, $this->settingTransformer);
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

        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $setting = $this->eloquentSettingRepository->findOne($id);

        if ( ! $setting instanceof Setting) {
            return $this->sendNotFoundResponse("The setting with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $setting);

        // User can modify only certain fields
        $newInput = $this->getSettingInputUpdate($request);

        $setting = $this->eloquentSettingRepository->update($setting, $newInput);

        return $this->respondWithItem($setting, $this->settingTransformer);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Insert default settings in database when user is created.
     *
     * @param $user
     * @param $setting
     *
     * @return bool
     */
    public function insertDefaultSetting($user, $setting)
    {
//        Not needed

//        $setting_exists = $this->eloquentSettingRepository->findOneBy([
//            'user_id' => $user->id
//        ]);
//
//        if ($setting_exists instanceof Setting) {
//            return false;
//        }

        $input['user_id']               = $user->id;
        $input['search_distance']       = config('default.setting.search_distance');
        $input['distance_in']           = config('default.setting.distance_in');
        $input['show_ages_min']         = config('default.setting.show_ages_min');
        $input['show_ages_max']         = config('default.setting.show_ages_max');
        $input['interested_in']         = $setting['interested_in'];
        $input['date_with']             = (isset($user) && $user['gender'] == 'MALE') ? 'FEMALE' : 'MALE';
        $input['privacy_show_distance'] = 1;
        $input['privacy_show_age']      = 1;

        $setting = $this->eloquentSettingRepository->save($input);

        if ( ! $setting instanceof Setting) {
            return false;
        }

        return true;
    }

    /**
     * Insert default setting when user is logged in by facebook
     *
     * @param $user
     *
     * @return bool
     */
    public function insertDefaultSettingFacebook($user)
    {
        $input['user_id']               = $user->id;
        $input['search_distance']       = config('default.setting.search_distance');
        $input['distance_in']           = config('default.setting.distance_in');
        $input['show_ages_min']         = config('default.setting.show_ages_min');
        $input['show_ages_max']         = config('default.setting.show_ages_max');
        $input['interested_in']         = config('default.setting.interested_in');
        $input['date_with']             = (isset($user) && strtoupper($user['gender']) == 'MALE') ? 'FEMALE' : 'MALE';
        $input['privacy_show_distance'] = 1;
        $input['privacy_show_age']      = 1;

        $setting = $this->eloquentSettingRepository->save($input);

        if ( ! $setting instanceof Setting) {
            return false;
        }

        return true;
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
            'search_distance'       => 'required|numeric|min:1|max:100',
            'distance_in'           => 'required|in:MI,KM',
            'show_ages_min'         => 'required|numeric|min:18|max:' . (int)$request->input('show_ages_max'),
            'show_ages_max'         => 'required|numeric|max:55',
            'interested_in'         => 'required|in:FRIENDSHIP,RELATIONSHIP,CASUAL_MEETUP',
            'date_with'             => 'required|in:MALE,FEMALE,BOTH',
            'privacy_show_distance' => 'boolean',
            'privacy_show_age'      => 'boolean'
        ];

        return $rules;
    }

    /**
     * Get setting of current user.
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getSettingUser()
    {
        $currentUser = $this->getCurrentUserDetails();

        $setting = $currentUser->setting()->get()->first();

        if ( ! $setting instanceof Setting) {
            return $this->sendNotFoundResponse("Setting for user id {$currentUser->uid} doesn't exist.");
        }

        return $this->respondWithItem($setting, $this->settingTransformer);
    }

    /**
     * Get settings from request  that can be changed
     *
     * @param Request $request
     *
     * @return array
     */
    private function getSettingInputUpdate(Request $request)
    {
        $userInput = $request->except([
            'id',
            'uid',
            'user_id',
        ]);

        $currentUser  = $this->getCurrentUserDetails();
        $subscription = $currentUser->subscription('main');

        // Here we prevent users from changing the privacy show age and distance to 0, if they are in standard plan
        $canUseDistancePrivacyOptions = $subscription->ability()->canUse('PRIVACY_SHOW_DISTANCE');

        if ( ! $canUseDistancePrivacyOptions) {
            $userInput['privacy_show_distance'] = 1;
        }
        $canUseAgePrivacyOptions = $subscription->ability()->canUse('PRIVACY_SHOW_AGE');
        if ( ! $canUseAgePrivacyOptions) {
            $userInput['privacy_show_age'] = 1;
        }

        return $userInput;
    }
}
