<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SwipeHelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\BoostProfile;
use App\Repositories\Contracts\BoostProfileRepository;
use App\Repositories\Contracts\LocationRepository;
use App\Repositories\Contracts\SettingRepository;
use App\Transformers\BoostProfileTransformer;
use App\Transformers\FeatureTransformer;
use App\Transformers\SwipeBoostProfileTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoostProfileController extends Controller
{
    private $featureTransformer;
    private $boostProfileRepository;
    private $boostProfileTransformer;
    private $settingRepository;
    private $locationRepository;
    private $swipeBoostProfileTransformer;

    public function __construct(
        FeatureTransformer $featureTransformer,
        BoostProfileRepository $boostProfileRepository,
        BoostProfileTransformer $boostProfileTransformer,
        SwipeBoostProfileTransformer $swipeBoostProfileTransformer,
        SettingRepository $settingRepository,
        LocationRepository $locationRepository

    ) {
        $this->featureTransformer           = $featureTransformer;
        $this->boostProfileRepository       = $boostProfileRepository;
        $this->boostProfileTransformer      = $boostProfileTransformer;
        $this->swipeBoostProfileTransformer = $swipeBoostProfileTransformer;
        $this->settingRepository            = $settingRepository;
        $this->locationRepository           = $locationRepository;

        parent::__construct();
    }

    public function index(Request $request)
    {
        $today        = (string)Carbon::now();
        $usersDetails = $this->getCurrentUserDetails();
        $settings     = $this->getSettings($usersDetails->id);
        $matchedUsers = SwipeHelperFunctions::getMatchedUsers($usersDetails);
        $location     = $this->getLocation($usersDetails->id);
        $distance     = SwipeHelperFunctions::haversine($location->latitude, $location->longitude, 'miles');


        $boost_profiles = DB::table('boost_profiles');
        $dateWith       = $settings->date_with;

        $query = $boost_profiles->where('boost_profiles.expired_at', '>', $today)->where('boost_profiles.user_id', '<>',
            $usersDetails->id)->whereNotIn('boost_profiles.user_id', $matchedUsers)->join('users',
            function ($join) use ($dateWith) {
                $join->on('boost_profiles.user_id', '=', 'users.id')->whereNull('users.deleted_at')->where(function (
                    $join
                ) use ($dateWith) {
                    switch ($dateWith) {
                        case 'MALE';
                            $join->where('gender', '=', 'MALE');
                            break;
                        case 'FEMALE':
                            $join->where('gender', '=', "FEMALE");
                            break;
                        case 'BOTH':
                            $join->whereIn('gender', ['MALE', 'FEMALE']);
                            break;
                    }
                });
            })->join('images', function ($join) {
            $join->on('boost_profiles.user_id', '=', 'images.user_id')->where('images.number', '=', 1);
        })->join('locations', function ($join) {
            $join->on('users.id', '=', 'locations.user_id');
        })->leftjoin('right_left_swipes', function ($join) use ($usersDetails) {
            $join->on('boost_profiles.user_id', '=', 'right_left_swipes.a')->where('right_left_swipes.b', '=',
                $usersDetails->id);
        })->join('settings', function ($join) use ($settings, $usersDetails) {
            $join->on('boost_profiles.user_id', '=', 'settings.user_id')->where('settings.interested_in', '=',
                $settings->interested_in)->where(function ($date_with) use ($usersDetails) {
                return $date_with->where('settings.date_with', '=',
                    $usersDetails->gender)->orWhere('settings.date_with', '=', 'BOTH');
            });
        })->select('users.id as user_user_id', 'users.uid as user_uid', 'users.name as user_name',
            'users.gender as user_gender', 'users.dob as user_dob', 'images.name as image_name',
            'images.path as image_path', 'images.number as image_number', 'images.link as image_link',
            'settings.interested_in as setting_interested_in', 'settings.date_with as setting_date_with',
            'settings.privacy_show_distance as setting_privacy_show_distance',
            'settings.privacy_show_age as setting_privacy_show_age', 'right_left_swipes.swipe_type',
            'boost_profiles.expired_at as boost_profiles_expired_at',
            DB::raw(SwipeHelperFunctions::constructQueryStringAge()), DB::raw($distance));

        $rawQuery = $this->getSql($query);

        $fullQuery = DB::table(DB::raw("(" . $rawQuery . ") as users"))->whereBetween('user_age', [
            $settings->show_ages_min,
            $settings->show_ages_max
        ]);
        if (config('demo.locationTurnOff')) {
            $fullQuery->where('location_distance', '<=', $settings->search_distance);
        };


        $users = $fullQuery->paginate(10);

        return $this->respondWithCollection($users, $this->swipeBoostProfileTransformer);

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

    public function store(Request $request)
    {
        $currentUser = $this->getCurrentUserDetails();

        $subscription = $currentUser->subscription('main');

        $currentUser->subscriptionUsage('main')->subscriptionFeatureUsageRenew('BOOST_PROFILE');

        $canUseBoost = $subscription->ability()->canUse('BOOST_PROFILE');

        if ( ! $canUseBoost) {
            $ability['BOOST_PROFILE'] = $subscription->ability()->getFeatureAbilityDetails('BOOST_PROFILE');

            $abilityDetails                = app();
            $abilityDetailsObject          = $abilityDetails->make('stdClass');
            $abilityDetailsObject->feature = $ability;

            return $this->respondWithItem($abilityDetailsObject, $this->featureTransformer);
        }

        $boostProfileRecord = $this->boostProfileRepository->findOneBy([
            'user_id' => $currentUser->id
        ]);

        if ( ! $boostProfileRecord instanceof BoostProfile) {
            $input = [
                'user_id'    => $currentUser->id,
                'expired_at' => (string)$this->getExpiryTime()
            ];

            $saveBoostProfile = $this->boostProfileRepository->save($input);

        } else {
            $input = [
                'expired_at' => (string)$this->getExpiryTime()
            ];

            $saveBoostProfile = $this->boostProfileRepository->update($boostProfileRecord, $input);
        }

        $currentUser->subscriptionUsage('main')->record('BOOST_PROFILE', 1);

        // Refresh User
        $currentUser  = Auth::user()->fresh();
        $subscription = $currentUser->subscription('main');

        $ability['BOOST_PROFILE'] = $subscription->ability()->getFeatureAbilityDetails('BOOST_PROFILE');

        $boostProfileDetails               = app();
        $boostProfileDetailObject          = $boostProfileDetails->make('stdClass');
        $boostProfileDetailObject->status  = 'Boost Successful.';
        $boostProfileDetailObject->feature = $ability;

        return $this->respondWithItem($boostProfileDetailObject, $this->boostProfileTransformer);
    }

    public function getExpiryTime()
    {
        // Get current date using Carbon
        $today = Carbon::now();


        return $today->addMinutes(30);
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
