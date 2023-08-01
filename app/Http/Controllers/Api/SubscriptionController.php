<?php

namespace App\Http\Controllers\Api;

use App\Events\GoogleInAppSubscriptionEvent;
use App\Http\Controllers\Controller;
use App\Models\AppleInAppSubscription;
use App\Models\GoogleInAppSubscription;
use App\Models\User;
use App\Repositories\Contracts\AppleInAppSubscriptionRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\EloquentGoogleInAppSubscriptionRepository;
use App\Repositories\EloquentUserRepository;
use App\Transformers\PlanSubscriptionTransformer;
use Cimons\LaraIosInApp\iTunes\PurchaseItem;
use Cimons\LaraIosInApp\iTunes\Validator as iTunesValidator;
use Gerardojbaez\Laraplans\Feature;
use Gerardojbaez\Laraplans\Models\Plan;
use Google\Cloud\PubSub\PubSubClient;
use Google_Client;
use Google_Service_AndroidPublisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Storage;

class SubscriptionController extends Controller
{
    private $planSubscriptionTransformer;
    private $planRepository;
    protected $eloquentGoogleInAppSubscriptionRepository;
    protected $appleInAppSubscriptionRepository;
    /**
     * @var EloquentUserRepository
     */
    protected $eloquentUserRepository;

    public function __construct(
        PlanRepository $planRepository,
        PlanSubscriptionTransformer $planSubscriptionTransformer,
        AppleInAppSubscriptionRepository $appleInAppSubscriptionRepository
    ) {
        $this->planSubscriptionTransformer      = $planSubscriptionTransformer;
        $this->planRepository                   = $planRepository;
        $this->appleInAppSubscriptionRepository = $appleInAppSubscriptionRepository;

        $this->eloquentGoogleInAppSubscriptionRepository = new EloquentGoogleInAppSubscriptionRepository(new GoogleInAppSubscription());
        $this->eloquentUserRepository                    = new EloquentUserRepository(new User());

        parent::__construct();
    }

    public function renewTest()
    {
        $user = $this->getCurrentUserDetails();
        $user->subscription('main')->renew();
    }

    /**
     * Renew Subscription
     *
     * @param $purchaseToken
     *
     * @return bool
     */
    public function renew($purchaseToken)
    {
        $this->debugLog('Inside Renew function', true);

        $user = $this->getUser($purchaseToken);
        if ( ! $user instanceof User) {
            return false;
        }

        $this->debugLog('Instance of user.', true);
        $user->subscription('main')->renew();

        // Refresh User
        $featureList = Feature::getAllFeatures();
        // Insert feature usage to 0
        foreach ($featureList as $featureName) {
            $user->subscriptionUsage('main')->record($featureName, 0, false);
        }

        $this->debugLog("Subscription Renewed for User Id: {$user->id} ", true, true);
    }

    /**
     * Get user according to purchase token received from google
     *
     * @param $purchaseToken
     *
     * @return bool|\Illuminate\Database\Eloquent\Model|mixed|null|static
     */
    public function getUser($purchaseToken)
    {
        $input = [
            'purchaseToken' => $purchaseToken
        ];

        $googlePurchaseInformation = $this->eloquentGoogleInAppSubscriptionRepository->findOneBy($input);

        if ( ! $googlePurchaseInformation instanceof GoogleInAppSubscription) {
            $this->debugLog("Google In App Subscription not found for the token.", false, true);

            return false;
        }

        $userInput = [
            'id' => $googlePurchaseInformation->user_id
        ];

        $user = $this->eloquentUserRepository->findOneBy($userInput);
        if ( ! $user instanceof User) {
            $this->debugLog("User not found for the Google In App Subscription token.", false, true);

            return false;
        }

        return $user;
    }

    /**
     * Cancel Subscription
     *
     * @param $purchaseToken
     *
     * @return bool
     */
    public function cancel($purchaseToken)
    {
        $this->debugLog("Inside Cancel function", true);

        $user = $this->getUser($purchaseToken);

        if ( ! $user instanceof User) {
            return false;
        }

        $this->debugLog("Instance of user.", true);

        $user->subscription('main')->cancel();
        $this->debugLog("Subscription Canceled for User Id: {$user->id}", true, true);
    }

    /**
     * Test Cancel Subscription
     *
     * @param Request $request
     */
    public function cancelTest(Request $request)
    {
        $user = $this->getCurrentUserDetails();

        if ($request->input('immediately') == true) {
            $user->subscription('main')->cancel('true');
        } else {
            $user->subscription('main')->cancel();
        }
    }

    /**
     * Test Resume Subscription
     */
    public function resumeTest()
    {
        $user = $this->getCurrentUserDetails();

        $user->subscription('main')->resume();
    }

    /**
     * Resume Subscription
     *
     * @param $purchaseToken
     *
     * @return bool
     */
    public function resume($purchaseToken)
    {
        $this->debugLog("Inside Resume function", true);

        $user = $this->getUser($purchaseToken);

        if ( ! $user instanceof User) {
            return false;
        }

        $user->subscription('main')->resume();
        $this->debugLog("Subscription Resumed for User Id: {$user->id}", true);
    }

    public function canSubscribeToOtherPlan()
    {
        $user = $this->getCurrentUserDetails();

        if ($user->subscribed('main')) {
            $status = $user->subscription('main')->canSubscribeToOtherPlan();
            if ($status) {
                return $this->sendCustomResponse('200', true);
            }

            return $this->sendCustomResponse('200', false);
        }

        return $this->sendCustomResponse('200', true);
    }

    /**
     * Store new subscription
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|string
     */
    public function store(Request $request)
    {
        $validatorResponse = $this->validateRequest($request, $this->storeRequestInAppValidationRules());

        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $user = $this->getCurrentUserDetails();

        if ($request->input('type') == 'ANDROID_PLAYSTORE') {
            $this->debugLog("Purchase from ANDROID. User Id = {$user->id}. Now verifying from Google", true);

            $this->verifyFromGoogle($request);
            $this->debugLog('Verification from Google Passed', true);

            $receipt = json_decode($request->input('receipt'));

            // Check if the purchase token is used.

            $orderId = $this->eloquentGoogleInAppSubscriptionRepository->findOneBy([
                'orderId' => $receipt->orderId
            ]);

            if ($orderId instanceof GoogleInAppSubscription) {
                return $this->sendNotFoundResponse('The order id has already been used.');
            }

            $plan = $this->planRepository->findOneBy([
                'google_product_id' => $receipt->productId
            ]);

            if ( ! $plan instanceof Plan) {
                $message = "The Plan for {$receipt->productId} received from google is not found. Please map the product id from google.";
                $this->debugLog($message, false, true);

                return $this->sendNotFoundResponse($message);
            }

            if ($user->subscribed('main')) {
                $status = $user->subscription('main')->canSubscribeToOtherPlan();
                if ( ! $status) {
                    $message = "You have already subscribed. You cannot subscribe more than once.";
                    $this->debugLog($message, false, true);

                    return $this->sendCustomResponse('200', $message);
                }
            }

            $subscription = $user->newSubscription('main', $plan)->create();

            // Now we set the subscription feature usage to 0 for all the features
            $featureList = Feature::getAllFeatures();
            // Refresh User
            $user = Auth::user()->fresh();

            foreach ($featureList as $featureName) {
                $user->subscriptionUsage('main')->record($featureName, 0, false);
            }

            $this->debugLog("New Subscription Created for plan {$plan->name} of interval {$plan->interval}. User Id = {$user->id}",
                true, true);
            // fire user created event
            event(new GoogleInAppSubscriptionEvent($subscription->id, $receipt));

            return $this->setStatusCode(201)->respondWithItem($subscription, $this->planSubscriptionTransformer);
        } else if ($request->input('type') == 'IOS_APPSTORE') {
            $this->debug_log_ios("Purchase from IOS. User Id = {$user->id}", true);

            $receipt = $request->input('receipt');
            $this->debug_log_ios("Receipt  User Id = {$user->id}. {$receipt}", true);

            if ((config('ios.app_shared_secret')) == null) {
                $this->debug_log_ios("IOS App shared secret has not been placed in env file", false, true);

                return $this->sendNotFoundResponse('IOS App shared secret has not been placed in env file');
            }

            if (config('ios.inapp_enviroment') == 'sandbox') {
                $validator = new iTunesValidator(iTunesValidator::ENDPOINT_SANDBOX);
            } else {
                $validator = new iTunesValidator(iTunesValidator::ENDPOINT_PRODUCTION);
            }

            try {
                $this->debug_log_ios('Now verifying from Apple', true);
                $sharedSecret = config('ios.app_shared_secret'); // Generated in iTunes Connect's In-App Purchase menu
                $response     = $validator->setSharedSecret($sharedSecret)->setReceiptData($receipt)->setExcludeOldTransactions('true')->validate(); // use setSharedSecret() if for recurring subscriptions
            } catch (\Exception $e) {
                $this->debug_log_ios("IOS got error = {$e->getMessage()}", false, true);

                return $this->sendNotFoundResponse("IOS got error = {$e->getMessage()}");
            }
            if ($response->isValid()) {
                $this->debug_log_ios('Receipt is valid.', true);
            } else {
                $this->debug_log_ios("Receipt is not valid.", false);
                $this->debug_log_ios("Receipt result code = {$response->getResultCode()}", false, true);

                return $this->sendNotFoundResponse("IOS Receipt result code =  {$response->getResultCode()}");
            }

            $latestReceiptInfo = $response->getLatestReceiptInfo();
            $latestReceiptInfo = new PurchaseItem($latestReceiptInfo[0]->getRawResponse());

//            $latestReceiptInfoTest = [
//                'quantity'                   => 1,
//                'product_id'                 => 'com.datingAppScript.Lovelock.lovelockplus_003',
//                'transaction_id'             => '1000000426359086',
//                'original_transaction_id'    => '1000000422964445',
//                'purchase_date'              => '2018-08-03 06:47:31 Etc/GMT',
//                'purchase_date_ms'           => '1533278851000',
//                'purchase_date_pst'          => '2018-08-02 23:47:31 America/Los_Angeles',
//                'original_purchase_date'     => '2018-07-26 09:59:03 Etc/GMT',
//                'original_purchase_date_ms'  => '1532599143000',
//                'original_purchase_date_pst' => '2018-07-26 02:59:03 America/Los_Angeles',
//                'expires_date'               => '2019-08-03 07:17:31 Etc/GMT',
//                'expires_date_ms'            => '1627981093000',
//                'expires_date_pst'           => '2018-08-03 00:17:31 America/Los_Angeles',
//                'web_order_line_item_id'     => '1000000039765424',
//                'is_trial_period'            => false,
//                'is_in_intro_offer_period'   => false,
//            ];
//
//
//            $latestReceiptInfo = new PurchaseItem($latestReceiptInfoTest);

            // We get Pending Auto renewal Status
            $pendingRenewalInfo = $response->getPendingRenewalInfo();
            $autoRenewStatus    = $pendingRenewalInfo[0]->getAutoRenewStatus();
//            $autoRenewStatus    = 1;

            $plan = $this->planRepository->findOneBy([
                'apple_product_id' => $latestReceiptInfo['product_id']
            ]);

            if ( ! $plan instanceof Plan) {
                $message = "The Plan for {$latestReceiptInfo->getProductId()} received from apple is not found. Please map the product id from apple.";
                $this->debug_log_ios($message, false, true);

                return $this->sendNotFoundResponse($message);
            }

            $subscriptionDetails = $user->subscription('main');

            if ($subscriptionDetails != '' && ! $subscriptionDetails->checkIfSubscribedToFreePlan()) {
                $existingAppleSubscriptionDetails = $this->appleInAppSubscriptionRepository->findOneBy([
                    'original_transaction_id' => $latestReceiptInfo->getOriginalTransactionId(),
                    'product_id'              => $latestReceiptInfo->getProductId(),
                    'plan_subscription_id'    => $subscriptionDetails->id
                ]);

                // If the ios receipt details such as product id and subscription_id are matched we will renew it
                if ($existingAppleSubscriptionDetails instanceof AppleInAppSubscription) {
                    $this->debug_log_ios("Apple Details found for Original Transaction Id = {$latestReceiptInfo->getOriginalTransactionId()}, Product id = {$latestReceiptInfo->getProductId()} and Subscription Id ={$subscriptionDetails->id}",
                        true);

                    $newInput = [
                        'receipt'           => $receipt,
                        'purchase_date'     => $latestReceiptInfo->getPurchaseDate(),
                        'expires_date'      => $latestReceiptInfo->getExpiresDate(),
                        'transaction_id'    => $latestReceiptInfo->getTransactionId(),
                        'auto_renew_status' => $autoRenewStatus
                    ];

                    // We only renew subscription if the expiry date received from the Receipt if greater that current subscription expiry date.
                    if ($latestReceiptInfo->getExpiresDate()->gt($existingAppleSubscriptionDetails->expires_date)) {
                        $this->appleInAppSubscriptionRepository->update($existingAppleSubscriptionDetails, $newInput);

                        $user->subscription('main')->renewIos($latestReceiptInfo->getPurchaseDate(),
                            $latestReceiptInfo->getExpiresDate());

                        // Refresh User
                        $featureList = Feature::getAllFeatures();
                        // Insert feature usage to 0
                        foreach ($featureList as $featureName) {
                            $user->subscriptionUsage('main')->record($featureName, 0, false);
                            $this->debug_log_ios("Feature Usage Renewed.", true);
                        }

                        $this->debug_log_ios("Subscription Renewed for User Id: {$user->id} ", true, true);

                        return $this->sendCustomResponse("200", "Your subscription has been renewed.");
                    }

                    return $this->sendCustomResponse("200", "No Renew Required.");
                }
            }

            if ($user->subscribed('main')) {
                $status = $user->subscription('main')->canSubscribeToOtherPlan();
                if ( ! $status) {
                    $message = "You have already subscribed. You cannot subscribe more than once.";
                    $this->debug_log_ios($message, false, true);

                    return $this->sendCustomResponse('200', $message);
                }
            }

            $subscription = $user->newSubscription('main', $plan)->create([
                'starts_at' => $latestReceiptInfo->getPurchaseDate(),
                'ends_at'   => $latestReceiptInfo->getExpiresDate()
            ]);

            $this->debug_log_ios("Subscription Created", true, true);

            // Now we set the subscription feature usage to 0 for all the features
            $featureList = Feature::getAllFeatures();
            // Refresh User
            $user = Auth::user()->fresh();

            foreach ($featureList as $featureName) {
                $user->subscriptionUsage('main')->record($featureName, 0, false);
            }

            $input                            = [];
            $input['user_id']                 = $user->id;
            $input['plan_subscription_id']    = $subscription->id;
            $input['product_id']              = $latestReceiptInfo->getProductId();
            $input['receipt_data']            = $receipt;
            $input['original_transaction_id'] = $latestReceiptInfo->getOriginalTransactionId();
            $input['transaction_id']          = $latestReceiptInfo->getTransactionId();
            $input['purchase_date']           = $latestReceiptInfo->getPurchaseDate();
            $input['expires_date']            = $latestReceiptInfo->getExpiresDate();
            $input['original_purchase_date']  = $latestReceiptInfo->getOriginalPurchaseDate();
            $input['auto_renew_status']       = $autoRenewStatus;
            $this->appleInAppSubscriptionRepository->save($input);

            return $this->setStatusCode(201)->respondWithItem($subscription, $this->planSubscriptionTransformer);
        }
    }

    /**
     * Use the service account and verify if the purchase is valid and came from the app and not other apps
     *
     * @param Request $request
     *
     * @return \Google_Service_AndroidPublisher_SubscriptionPurchase
     */
    public function verifyFromGoogle(Request $request)
    {
//        $path = storage_path('service-account.json');

        $client = new Google_Client();

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path() . '/' . config('pubsub.connections.gcloud.key_file'));

        $client->setApplicationName(config('app.name'));
//        $client->setAuthConfig($path);

        $client->useApplicationDefaultCredentials();
        $client->addScope('https://www.googleapis.com/auth/androidpublisher');

        $service = new Google_Service_AndroidPublisher($client);

        $receipt = json_decode($request->input('receipt'));

        $subscription = $service->purchases_subscriptions->get($receipt->packageName, $receipt->productId,
            $receipt->purchaseToken);

        return $subscription;

    }

    /**
     * Handle the request received from google
     */
    public function pubSubRequest()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path() . '/' . config('pubsub.connections.gcloud.key_file'));

        $pubsub = new PubSubClient([
            'projectId' => config('pubsub.connections.gcloud.project_id')
        ]);

        $httpPostRequestBody = file_get_contents('php://input');
        $requestData         = json_decode($httpPostRequestBody, true);

        $message = $pubsub->consume($requestData);

        $this->debugLog($message->data(), true);

        $messageArray = json_decode($message->data(), true);

        $this->debugLog($messageArray, true);

        $notificationType = $messageArray['subscriptionNotification']['notificationType'];
        $purchaseToken    = $messageArray['subscriptionNotification']['purchaseToken'];

        switch ($notificationType) {

            case '2':
                $this->debugLog("Renew Subscription", true);
                $this->renew($purchaseToken);
                break;
            case '3':
                $this->debugLog("Cancel Subscription", true);
                $this->cancel($purchaseToken);
                break;
            case '7':
                $this->debugLog("Resume Subscription", true);
                $this->resume($purchaseToken);
                break;
            default:
                $this->debugLog("Unknown notification type: {$notificationType}", false, true);
                break;
        }
    }

    public function storeRequestInAppValidationRules()
    {
        $rules = [
            'type'    => 'required|in:ANDROID_PLAYSTORE,IOS_APPSTORE',
            'receipt' => 'required'
        ];

        return $rules;
    }

    /**
     * This logs the purchase info into a file
     *
     * @param $message string The message to be logged.
     * @param $success bool Success or Failure
     * @param bool $end ends the message with dash
     */
    public function debugLog($message, $success, $end = false)
    {
        Log::useFiles(storage_path() . '/logs/googlePubSub.log');

//        $text = '[' . date('m/d/Y g:i:s A') . '] - ' . (($success) ? 'SUCCESS :' : 'FAILURE :') . $message . "\n";
//        if ($end) {
//            $text .= "\n------------------------------------------------------------------\n\n";
//        }

        Log::info($message);

        return;
    }

    public function debug_log_ios($message, $success, $end = false)
    {
        Log::useFiles(storage_path() . '/logs/iosCallback.log');

//        $text = '[' . date('m/d/Y g:i:s A') . '] - ' . (($success) ? 'SUCCESS :' : 'FAILURE :') . $message . "\n";
//        if ($end) {
//            $text .= "\n------------------------------------------------------------------\n\n";
//        }

        Log::info($message);

        return;
    }

    public function testSubscribe()
    {

        $currentUser = $this->getCurrentUserDetails();
        $plan        = Plan::where('plan_code', '=', 'plus_plan_1')->first();

        $currentUser->newSubscription('main', $plan)->create();
        $currentUser->subscriptionUsage('main')->record('LIKE', 0, false);
        $currentUser->subscriptionUsage('main')->record('SUPER_LIKE', 0, false);
        $currentUser->subscriptionUsage('main')->record('REWIND_SWIPE', 0, false);
    }
}

