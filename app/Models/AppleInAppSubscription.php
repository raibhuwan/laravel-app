<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppleInAppSubscription extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_date'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'user_id',
        'plan_subscription_id',
        'product_id',
        'receipt_data',
        'original_transaction_id',
        'transaction_id',
        'purchase_date',
        'expires_date',
        'original_purchase_date',
        'auto_renew_status',
    ];
}
