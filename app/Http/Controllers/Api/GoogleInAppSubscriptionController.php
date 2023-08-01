<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleInAppSubscription;
use App\Repositories\EloquentGoogleInAppSubscriptionRepository;
use Carbon\Carbon;

class GoogleInAppSubscriptionController extends Controller
{
    protected $eloquentGoogleInAppSubscriptionRepository;

    public function __construct()
    {
        $this->eloquentGoogleInAppSubscriptionRepository = new EloquentGoogleInAppSubscriptionRepository(new GoogleInAppSubscription());
        parent::__construct();
    }

    /**
     * Create new google in app subscription record
     *
     * @param $subscription
     * @param $receipt
     */
    public function store($subscription, $receipt)
    {
        $input                         = [];
        $input['user_id']              = $this->getCurrentUserDetails()->id;
        $input['plan_subscription_id'] = $subscription;
        $input['orderId']              = $receipt->orderId;
        $input['packageName']          = $receipt->packageName;
        $input['productId']            = $receipt->productId;

        $input['purchaseTime']  = Carbon::createFromTimestamp(substr($receipt->purchaseTime, 0,
            -3))->toDateTimeString();
        $input['purchaseToken'] = $receipt->purchaseToken;
        $input['autoRenewing']  = $receipt->autoRenewing;

        // Convert from Std object to array and save
        $this->eloquentGoogleInAppSubscriptionRepository->save($input);
    }

}
