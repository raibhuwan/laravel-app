<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppleInAppSubscription;
use App\Models\Setting;
use App\Repositories\Contracts\AppleInAppSubscriptionRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\SettingRepository;
use App\Transformers\FeatureTransformer;
use Cimons\LaraIosInApp\iTunes\PurchaseItem;
use Cimons\LaraIosInApp\iTunes\Validator as iTunesValidator;
use Gerardojbaez\Laraplans\Feature;
use Gerardojbaez\Laraplans\Models\Plan;
use Gerardojbaez\Laraplans\Models\PlanSubscriptionUsage;
use Illuminate\Support\Facades\Auth;

class FeatureController extends Controller
{
    private $featureTransformer;
    private $settingRepository;
    private $appleInAppSubscriptionRepository;
    private $planRepository;

    public function __construct(
        FeatureTransformer $featureTransformer,
        SettingRepository $settingRepository,
        AppleInAppSubscriptionRepository $appleInAppSubscriptionRepository,
        PlanRepository $planRepository
    ) {
        $this->featureTransformer               = $featureTransformer;
        $this->settingRepository                = $settingRepository;
        $this->appleInAppSubscriptionRepository = $appleInAppSubscriptionRepository;
        $this->planRepository                   = $planRepository;

        parent::__construct();
    }

    /**
     * Get features list with details  (can use , remaining uses and used details)
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getFeatures()
    {
        $currentUser = $this->getCurrentUserDetails();

        $featureList = Feature::getAllFeatures();

        $subscriptionDetails = $currentUser->subscription('main');

        // We first check if the user is subscribed using apple inapp, then we renew using receipt if it is expired.
        if ($subscriptionDetails != '' && ! $subscriptionDetails->checkIfSubscribedToFreePlan()) {
            if ($subscriptionDetails->isEnded()) {
                $this->checkIfSubscribedByApple($currentUser, $subscriptionDetails);
            }
        }

        if ( ! $currentUser->subscribed('main')) {
            // If the free plan is expired we just renew it
            if ($this->checkIfSubscribedToFreePlan($currentUser)) {
                $currentUser->subscription('main')->renew();
            } else {
                // Here if the premium plans are expired we assign free standard plan
                $plan = Plan::where('plan_code', '=', 'standard_plan')->first();
                $currentUser->newSubscription('main', $plan)->create();

                // Now we turn off the show distance and show age option
                $this->turnOffDistancePrivacySettings($currentUser->id);
            }
            // Refresh User
            $currentUser = Auth::user()->fresh();
            // Insert feature usage to 0
            foreach ($featureList as $featureName) {
                $currentUser->subscriptionUsage('main')->record($featureName, 0, false);
            }
        }

        // We check if the subscription usage date is expired and renew them
        $currentUser->subscriptionUsage('main')->subscriptionUsageRenew();

        $ability = [];

        $subscription = $currentUser->subscription('main');

        // Add feature usage when feature is added later..
        $featureUsage = PlanSubscriptionUsage::where('subscription_id', $subscription->id)->get();

        $featureUsageList = [];

        foreach ($featureUsage as $key => $value) {
            $featureUsageList[$key] = $value->code;
        }

        foreach ($featureList as $featureName) {
            if ( ! in_array($featureName, $featureUsageList)) {
                $currentUser->subscriptionUsage('main')->record($featureName, 0, false);
            }
        }

        foreach ($featureList as $featureName) {
            $ability[$featureName] = $subscription->ability()->getFeatureAbilityDetails($featureName);
        }

        $abilityDetails                = app();
        $abilityDetailsObject          = $abilityDetails->make('stdClass');
        $abilityDetailsObject->feature = $ability;

        return $this->respondWithItem($abilityDetailsObject, $this->featureTransformer);
    }

    /**
     * When plan is  automatically changed to free we need to turn off the privacy settings
     *
     * @param $userId
     *
     * @return bool
     */
    public function turnOffDistancePrivacySettings($userId)
    {

        $setting = $this->settingRepository->findOneBy([
            'user_id' => $userId
        ]);

        if ( ! $setting instanceof Setting) {
            return false;
        }

        $newInput = [
            'privacy_show_distance' => 1,
            'privacy_show_age'      => 1
        ];

        $this->settingRepository->update($setting, $newInput);

        return true;
    }

    /**
     * Check if the user is subscribed to free plan
     *
     * @param $currentUser
     *
     * @return bool
     */
    public function checkIfSubscribedToFreePlan($currentUser)
    {
        $currentPlan = $currentUser->subscription('main');

        if ( ! $currentPlan) {
            return false;
        }

        $plan = Plan::find($currentPlan->plan_id);

        if ($plan->plan_code == 'standard_plan') {
            return true;
        }

        return false;
    }

    /**
     * Check if the user is subscribed by apple, if then renew it using receipt
     *
     * @param $currentUser
     * @param $subscriptionDetails
     *
     * @return bool
     */
    public function checkIfSubscribedByApple($currentUser, $subscriptionDetails)
    {
        $plan = $this->planRepository->findOneBy([
            'id' => $subscriptionDetails->plan_id
        ]);

        $existingAppleSubscriptionDetails = $this->appleInAppSubscriptionRepository->findOneBy([
            'product_id'           => $plan->apple_product_id,
            'plan_subscription_id' => $subscriptionDetails->id
        ]);

        if ($existingAppleSubscriptionDetails instanceof AppleInAppSubscription) {
            if (config('ios.inapp_enviroment') == 'sandbox') {
                $validator = new iTunesValidator(iTunesValidator::ENDPOINT_SANDBOX);
            } else {
                $validator = new iTunesValidator(iTunesValidator::ENDPOINT_PRODUCTION);
            }

            try {
                $sharedSecret = config('ios.app_shared_secret'); // Generated in iTunes Connect's In-App Purchase menu
                $response     = $validator->setSharedSecret($sharedSecret)->setReceiptData($existingAppleSubscriptionDetails->receipt_data)->setExcludeOldTransactions('true')->validate(); // use setSharedSecret() if for recurring subscriptions
            } catch (\Exception $e) {
                return false;
            }
            if ( ! $response->isValid()) {
                return false;
            }

            $latestReceiptInfo = $response->getLatestReceiptInfo();
            $latestReceiptInfo = new PurchaseItem($latestReceiptInfo[0]->getRawResponse());

            // We get Pending Auto renewal Status
            $pendingRenewalInfo = $response->getPendingRenewalInfo();
            $autoRenewStatus    = $pendingRenewalInfo[0]->getAutoRenewStatus();

//            $latestReceiptInfoTest = [
//                'quantity'                   => 1,
//                'product_id'                 => 'com.datingAppScript.Lovelock.lovelockplus_001',
//                'transaction_id'             => '1000000426359086',
//                'original_transaction_id'    => '1000000422964445',
//                'purchase_date'              => '2018-08-03 06:47:31 Etc/GMT',
//                'purchase_date_ms'           => '1533278851000',
//                'purchase_date_pst'          => '2018-08-02 23:47:31 America/Los_Angeles',
//                'original_purchase_date'     => '2018-07-26 09:59:03 Etc/GMT',
//                'original_purchase_date_ms'  => '1532599143000',
//                'original_purchase_date_pst' => '2018-07-26 02:59:03 America/Los_Angeles',
//                'expires_date'               => '2019-08-03 07:17:31 Etc/GMT',
//                'expires_date_ms'            => '1659517093000',
//                'expires_date_pst'           => '2018-08-03 00:17:31 America/Los_Angeles',
//                'web_order_line_item_id'     => '1000000039765424',
//                'is_trial_period'            => false,
//                'is_in_intro_offer_period'   => false,
//            ];
//            $latestReceiptInfo     = new PurchaseItem($latestReceiptInfoTest);

            // We only renew subscription if the expiry date received from the Receipt if greater that current subscription expiry date.
            if (($latestReceiptInfo->getExpiresDate()->gt($existingAppleSubscriptionDetails->expires_date)) && ($latestReceiptInfo->getProductId() == $existingAppleSubscriptionDetails->product_id)) {

                $newInput = [
                    'transaction_id'    => $latestReceiptInfo->getTransactionId(),
                    'purchase_date'     => $latestReceiptInfo->getPurchaseDate(),
                    'expires_date'      => $latestReceiptInfo->getExpiresDate(),
                    'auto_renew_status' => $autoRenewStatus
                ];

                $this->appleInAppSubscriptionRepository->update($existingAppleSubscriptionDetails, $newInput);

                $currentUser->subscription('main')->renewIos($latestReceiptInfo->getPurchaseDate(),
                    $latestReceiptInfo->getExpiresDate());

                // Refresh User
                $featureList = Feature::getAllFeatures();
                // Insert feature usage to 0
                foreach ($featureList as $featureName) {
                    $currentUser->subscriptionUsage('main')->record($featureName, 0, false);
                }

                return true;
            }

            return false;
        }

        return false;
    }
}
