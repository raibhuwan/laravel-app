<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FcmHelperFunctions;
use App\Http\Controllers\Controller;
use App\Models\RightLeftSwipe;
use App\Models\User;
use App\Repositories\Contracts\RightLeftSwipeRepository;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\FeatureTransformer;
use App\Transformers\RightLeftSwipeTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaravelFCM\Message\PayloadDataBuilder;

class RightLeftSwipeController extends Controller
{
    private $rightLeftSwipeRepository;
    private $userRepository;
    private $rightLeftSwipeTransformer;
    private $featureTransformer;

    public function __construct(
        RightLeftSwipeRepository $rightLeftSwipeRepository,
        UserRepository $userRepository,
        RightLeftSwipeTransformer $rightLeftSwipeTransformer,
        FeatureTransformer $featureTransformer
    ) {
        $this->rightLeftSwipeRepository  = $rightLeftSwipeRepository;
        $this->userRepository            = $userRepository;
        $this->rightLeftSwipeTransformer = $rightLeftSwipeTransformer;
        $this->featureTransformer        = $featureTransformer;

        parent::__construct();
    }

    public function store(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user = $this->userRepository->findOne($request->input('user_id'));

        if ( ! $user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$request->input('user_id')} doesn't exist");
        }

        $currentUser = $this->getCurrentUserDetails();
        $swipeType   = $request->input('swipe_type');

        $subscription = $currentUser->subscription('main');

        switch ($swipeType) {
            case 'LIKE':
                // Renew Subscription usage if necessary
                $currentUser->subscriptionUsage('main')->subscriptionFeatureUsageRenew('LIKE');

                $canUseLike = $subscription->ability()->canUse('LIKE');


                if ( ! $canUseLike) {
                    $ability['LIKE'] = $subscription->ability()->getFeatureAbilityDetails('LIKE');

                    $abilityDetails                = app();
                    $abilityDetailsObject          = $abilityDetails->make('stdClass');
                    $abilityDetailsObject->feature = $ability;

                    return $this->respondWithItem($abilityDetailsObject, $this->featureTransformer);
                }
                break;
            case 'SUPER_LIKE':
                // Renew Subscription usage if necessary
                $currentUser->subscriptionUsage('main')->subscriptionFeatureUsageRenew('SUPER_LIKE');

                $canUseLike = $subscription->ability()->canUse('SUPER_LIKE');


                if ( ! $canUseLike) {
                    $ability['SUPER_LIKE'] = $subscription->ability()->getFeatureAbilityDetails('SUPER_LIKE');

                    $abilityDetails                = app();
                    $abilityDetailsObject          = $abilityDetails->make('stdClass');
                    $abilityDetailsObject->feature = $ability;

                    return $this->respondWithItem($abilityDetailsObject, $this->featureTransformer);
                }
                break;
        }


        $swipeRecord = $this->rightLeftSwipeRepository->findOneBy([
            'a' => $currentUser->id,
            'b' => $user->id
        ]);

        if ( ! $swipeRecord instanceof RightLeftSwipe) {
            $input = [
                'a'          => $currentUser->id,
                'b'          => $user->id,
                'swipe_type' => $swipeType,
                'expired_at' => (string)$this->getExpiryTime()
            ];


            /*
             * This is for automatic matching the users
             * in demo site
             * */
            if (config('demo.automaticMatch')) {
                $dummy  = [
                    'a'          => $user->id,
                    'b'          => $currentUser->id,
                    'swipe_type' => $swipeType,
                    'expired_at' => (string)$this->getExpiryTime()
                ];
                $randNo = rand(2, 3);
                if ($user->id % $randNo == 0) {

                    $temp = $this->rightLeftSwipeRepository->findOneBy([
                        'a' => $dummy['a'],
                        'b' => $dummy['b']
                    ]);

                    if ( ! $temp instanceof RightLeftSwipe) {
                        $this->rightLeftSwipeRepository->save($dummy);
                    }
                }
            }
            $saveSwipe = $this->rightLeftSwipeRepository->save($input);

            if ( ! $saveSwipe instanceof RightLeftSwipe) {
                return $this->sendCustomResponse(500, 'Error occurred on saving swipes.');
            }

        } else {
            $newInput = [
                'swipe_type' => $swipeType,
                'expired_at' => (string)$this->getExpiryTime()
            ];

            $this->rightLeftSwipeRepository->update($swipeRecord, $newInput);

        }

        $otherUserSwipeRecord = $this->rightLeftSwipeRepository->findOneBy([
            'a' => $user->id,
            'b' => $currentUser->id
        ]);

        $swipeDetails       = app();
        $swipeDetailsObject = $swipeDetails->make('stdClass');

        $swipeDetailsObject->swiped_user_id         = $request->input('user_id');
        $swipeDetailsObject->swiped_user_swipe_type = isset($otherUserSwipeRecord->swipe_type) ? ($otherUserSwipeRecord->swipe_type) : '';
        $swipeDetailsObject->your_swipe_type        = $swipeType;


        switch ($swipeType) {
            case 'LIKE':
                $currentUser->subscriptionUsage('main')->record('LIKE', 1);
                break;
            case 'SUPER_LIKE':
                $currentUser->subscriptionUsage('main')->record('SUPER_LIKE', 1);
                break;
        }

        // Refresh User
        $currentUser  = Auth::user()->fresh();
        $subscription = $currentUser->subscription('main');

        switch ($swipeType) {
            case 'LIKE':

                break;
            case 'SUPER_LIKE':

                $appName = config('app.name');
                $message = "Someone has Super Liked you on {$appName}.";;

                $fields = array(
                    'to'   => $user->fcm_registration_id,
                    'data' => $message,
                );

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData(['type' => 'SUPER_LIKE']);
                $dataBuilder->addData(['creator_id' => $currentUser->uid]);

                if ($fields['to'] != null) {
                    $fcm = FcmHelperFunctions::sendFcm($message, $fields, $dataBuilder);
                }

                break;
        }

        $ability['LIKE']       = $subscription->ability()->getFeatureAbilityDetails('LIKE');
        $ability['SUPER_LIKE'] = $subscription->ability()->getFeatureAbilityDetails('SUPER_LIKE');

        $swipeDetailsObject->feature = $ability;

        return $this->respondWithItem($swipeDetailsObject, $this->rightLeftSwipeTransformer);
    }

    private function getExpiryTime()
    {
        // Get current date using Carbon
        $today = Carbon::now();


        return $today->addMinutes(5);
    }

    private function storeRequestValidationRules()
    {
        $rules = [
            'swipe_type' => 'required|in:LIKE,NOPE,SUPER_LIKE',
            'user_id'    => 'required'
        ];

        return $rules;
    }
}
