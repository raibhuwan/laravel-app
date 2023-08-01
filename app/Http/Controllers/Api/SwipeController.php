<?php

namespace App\Http\Controllers\Api;

use App\Helpers\HelperFunctions;
use App\Helpers\SwipeHelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\ImageRepository;
use App\Repositories\Contracts\LocationRepository;
use App\Repositories\Contracts\RightLeftSwipeRepository;
use App\Repositories\Contracts\SettingRepository;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\SwipeTransformer;
use App\Transformers\SwipeUserFullDetailsTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwipeController extends Controller
{
    private $userRepository;
    private $swipeTransformer;
    private $locationRepository;
    private $settingRepository;
    private $imageRepository;
    private $swipeUserFullDetailsTransformer;
    private $rightLeftSwipeRepository;

    /**
     * base URL
     */
    const API_BASE_URL = "https://maps.googleapis.com/maps/api/geocode/";

    public function __construct(
        UserRepository $userRepository,
        SwipeTransformer $swipeTransformer,
        LocationRepository $locationRepository,
        SettingRepository $settingRepository,
        ImageRepository $imageRepository,
        SwipeUserFullDetailsTransformer $swipeUserFullDetailsTransformer,
        RightLeftSwipeRepository $rightLeftSwipeRepository

    ) {
        $this->userRepository = $userRepository;

        $this->locationRepository              = $locationRepository;
        $this->settingRepository               = $settingRepository;
        $this->imageRepository                 = $imageRepository;
        $this->swipeTransformer                = $swipeTransformer;
        $this->swipeUserFullDetailsTransformer = $swipeUserFullDetailsTransformer;
        $this->rightLeftSwipeRepository        = $rightLeftSwipeRepository;

        parent::__construct();
    }

    /**
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        DB::connection()->enableQueryLog();

        $usersDetails = $this->getCurrentUserDetails();
        $swipedUsers  = $this->getSwipedUsers($usersDetails);
        $matchedUsers = SwipeHelperFunctions::getMatchedUsers($usersDetails);
        $settings     = $this->getSettings($usersDetails->id);
        $location     = $this->getLocation($usersDetails->id);

        $distance = SwipeHelperFunctions::haversine($location->latitude, $location->longitude, 'miles');

        $users = DB::table('users');

        $users = $this->constructQueryStringDateWith($users, $settings->date_with);
        DB::connection()->enableQueryLog();

        $query = $users->where('users.id', '<>', $usersDetails->id)->whereNull('users.deleted_at')->join('images',
            function ($join) {
                $join->on('users.id', '=', 'images.user_id')->where('images.number', '=', 1);
            })->join('locations', function ($join) {
            $join->on('users.id', '=', 'locations.user_id');
        })->join('settings', function ($join) use ($settings, $usersDetails) {
            $join->on('users.id', '=', 'settings.user_id')->where('settings.interested_in', '=',
                $settings->interested_in)->where(function ($date_with) use ($usersDetails) {
                return $date_with->where('settings.date_with', '=',
                    $usersDetails->gender)->orWhere('settings.date_with', '=', 'BOTH');
            });
        })->leftjoin('right_left_swipes', function ($join) use ($usersDetails) {
            $join->on('users.id', '=', 'right_left_swipes.a')->where('right_left_swipes.b', '=', $usersDetails->id);
        })->select('users.id as user_user_id', 'users.uid as user_uid', 'users.name as user_name',
            'users.gender as user_gender', 'users.dob as user_dob', 'images.name as image_name',
            'images.path as image_path', 'images.number as image_number', 'images.link as image_link',
            'settings.interested_in as setting_interested_in', 'settings.date_with as setting_date_with',
            'settings.privacy_show_distance as setting_privacy_show_distance',
            'settings.privacy_show_age as setting_privacy_show_age', 'right_left_swipes.swipe_type',
            DB::raw(SwipeHelperFunctions::constructQueryStringAge()), DB::raw($distance));

//          ->having('location_distance', '<=',
//            $settings->search_distance)->having('user_age', '>=', $settings->show_ages_min)->having('user_age', '<=',
//            $settings->show_ages_max)->orderBy('location_distance', 'ASC')->get();
        $rawQuery = $this->getSql($query);

        //when location is turned off then executes this
        if (config('demo.locationTurnOff')) {
            $fullQuery = DB::table(DB::raw("(" . $rawQuery . ") as users"))// Show users less that the specified distance
                           ->whereBetween('user_age', [$settings->show_ages_min, $settings->show_ages_max]);
        } else {
            $fullQuery = DB::table(DB::raw("(" . $rawQuery . ") as users"))// Show users less that the specified distance
                           ->where('location_distance', '<=',
                $settings->search_distance)// Show user between the specified age range
                           ->whereBetween('user_age', [$settings->show_ages_min, $settings->show_ages_max]);
        }
        // Remove users that has already been swiped, matched

        $fullQuery->whereNotIn('user_user_id', array_merge($swipedUsers, $matchedUsers));

        $users = $fullQuery->paginate(10);

        //when location is turned off then executes this
        if (config('demo.locationTurnOff')) {
            foreach ($users as $key => $value) {
                $users[$key]->location_distance = rand(0, $settings->search_distance);
            }
        }

        $results = DB::getQueryLog();

        return $this->respondWithCollection($users, $this->swipeTransformer);
    }

    /**
     * Get users that has already been swiped
     *
     * @param $usersDetails
     *
     * @return array|bool
     */

    public function getSwipedUsers($usersDetails)
    {
        $swipeRecord = DB::table('right_left_swipes')->where('a', '=', $usersDetails->id)->get();
        $userIds     = [];
        $i           = 0;

        foreach ($swipeRecord as $key => $value) {
            if (HelperFunctions::checkDateExpiration($value->expired_at)) {
                $userIds[$i] = $value->b;
                $i++;
            }
        }

        return $userIds;
    }


    public function constructQueryStringDateWith($users, $dateWith)
    {
        switch ($dateWith) {

            case 'MALE';
                return $users->where('gender', '=', 'MALE');

            case 'FEMALE':
                return $users->where('gender', '=', "FEMALE");

            case 'BOTH':
                return $users->whereIn('gender', ['MALE', 'FEMALE']);
        }
    }

    /**
     * Get the full user details
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getUserFullDetails($id)
    {
        $users = $this->userRepository->findOne($id);

        if ( ! $users instanceof User) {
            return $this->sendNotFoundResponse("The user with id: {$id} doesn't exist");
        }

        $users = DB::table('users')->where('users.uid', '=', $id)->join('settings', function ($join) {
            $join->on('users.id', '=', 'settings.user_id');
        })->leftjoin('sounds', function ($join) {
            $join->on('users.id', '=', 'sounds.user_id');
        })->select('users.id as user_id', 'users.uid as user_uid', 'users.name as user_name',
            'users.gender as user_gender', 'users.dob as user_dob', 'users.about_me as user_about_me',
            'users.school as user_school', 'users.work as user_work',
            'settings.privacy_show_age as setting_privacy_show_age', 'sounds.name as sound_name',
            'sounds.path as sound_path')->get()->first();

//        $users = DB::table('users')->where('users.uid', '=', $id)->join('images', function ($join) {
//            $join->on('users.id', '=', 'images.user_id');
//        })->select('users.uid as user_uid', 'users.name as user_name', 'users.gender as user_gender',
//            'users.dob as user_dob', 'users.about_me as user_about_me', 'users.school as user_school',
//            'users.work as user_work', DB::raw("group_concat(images.path) as images_path"),
//            DB::raw("group_concat(images.number) as images_number"))->get();

        $images = DB::table('images')->where('images.user_id', '=', $users->user_id)->orderBy('number')->paginate();

        $imageNumber = 0;
        $newImage    = [];

        foreach ($images as $image) {
            $newImage[$imageNumber]['path']   = ($image->link == 1) ? $image->path : url('/') . '/storage/' . $image->path . $image->name;
            $newImage[$imageNumber]['number'] = $image->number;
            $imageNumber++;
        }

        $userDetails                      = app();
        $userDetailsObject                = $userDetails->make('stdClass');
        $userDetailsObject->user_uid      = $users->user_uid;
        $userDetailsObject->user_name     = $users->user_name;
        $userDetailsObject->user_gender   = $users->user_gender;
        $userDetailsObject->user_dob      = ($users->setting_privacy_show_age == 1) ? $users->user_dob : '';
        $userDetailsObject->user_age      = ($users->setting_privacy_show_age == 1) ? $this->getAge($users->user_dob) : '';
        $userDetailsObject->user_about_me = $users->user_about_me;
        $userDetailsObject->user_school   = $users->user_school;
        $userDetailsObject->user_work     = $users->user_work;
        $userDetailsObject->images        = $newImage;
        $userDetailsObject->sounds        = isset($users->sound_name) ? url('/') . '/storage/' . $users->sound_path . $users->sound_name : '';

        return $this->respondWithItem($userDetailsObject, $this->swipeUserFullDetailsTransformer);
    }

    /**
     * Convert DOB to age
     *
     * @param $age
     *
     * @return int
     */
    public function getAge($age)
    {
        $dt = Carbon::parse($age);

        return Carbon::createFromDate($dt->year, $dt->month, $dt->day)->age;
    }

    /**
     * Receive settings
     *
     * @param $id
     *
     * @return mixed
     */
    public function getSettings($id)
    {
        $settings = $this->settingRepository->findOneBy([
            'user_id' => $id
        ]);

        return $settings;
    }

    /**
     * Get location
     *
     * @param $id
     *
     * @return mixed
     */
    public function getLocation($id)
    {
        $locations = $this->locationRepository->findOneBy([
            'user_id' => $id
        ]);

        return $locations;
    }

}
