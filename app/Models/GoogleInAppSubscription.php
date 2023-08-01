<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleInAppSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'user_id',
        'plan_subscription_id',
        'orderId',
        'packageName',
        'productId',
        'purchaseTime',
        'purchaseToken',
        'autoRenewing'
    ];

}
