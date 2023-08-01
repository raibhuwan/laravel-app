<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\UserRepository;
use App\Transformers\FeatureTransformer;
use App\Transformers\RewindSwipeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewindSwipeController extends Controller
{
    private $userRepository;
    private $featureTransformer;
    private $rewindSwipeTransformer;

    public function __construct(
        UserRepository $userRepository,
        FeatureTransformer $featureTransformer,
        RewindSwipeTransformer $rewindSwipeTransformer
    ) {
        $this->userRepository         = $userRepository;
        $this->featureTransformer     = $featureTransformer;
        $this->rewindSwipeTransformer = $rewindSwipeTransformer;

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

        $subscription = $currentUser->subscription('main');

        // Renew Subscription usage if necessary
        $currentUser->subscriptionUsage('main')->subscriptionFeatureUsageRenew('REWIND_SWIPE');

        $canUseRewindSwipe = $subscription->ability()->canUse('REWIND_SWIPE');

        if ( ! $canUseRewindSwipe) {
            $ability['REWIND_SWIPE'] = $subscription->ability()->getFeatureAbilityDetails('REWIND_SWIPE');

            $abilityDetails                = app();
            $abilityDetailsObject          = $abilityDetails->make('stdClass');
            $abilityDetailsObject->feature = $ability;

            return $this->respondWithItem($abilityDetailsObject, $this->featureTransformer);
        }

        $currentUser->subscriptionUsage('main')->record('REWIND_SWIPE', 1);

        $rewindSwipeDetails       = app();
        $rewindSwipeDetailsObject = $rewindSwipeDetails->make('stdClass');

        $rewindSwipeDetailsObject->user_id    = $request->input('user_id');
        $rewindSwipeDetailsObject->swipe_type = 'REWIND_SWIPE';

        // Refresh User
        $currentUser = Auth::user()->fresh();
        $subscription = $currentUser->subscription('main');
        $ability['REWIND_SWIPE']                      = $subscription->ability()->getFeatureAbilityDetails('REWIND_SWIPE');

        $rewindSwipeDetailsObject->feature    = $ability;

        return $this->respondWithItem($rewindSwipeDetailsObject, $this->rewindSwipeTransformer);
    }

    private function storeRequestValidationRules()
    {
        $rules = [
            'user_id' => 'required'
        ];

        return $rules;
    }
}
