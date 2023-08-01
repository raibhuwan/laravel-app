<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plans
    |--------------------------------------------------------------------------
    | Contains the Plans Datails of the Android
    | and IOS
    |
    */

    'standard' => [
        'name'              => env('PLAN_SUBSCRIPTION_NAME0', 'LoveLock Standard'),
        'plan_code'         => env('PLAN_SUBSCRIPTION_PLAN_CODE0', 'standard_plan'),
        'description'       => env('PLAN_SUBSCRIPTION_DESCRIPTION0', 'Standard plan'),
        'price'             => env('PLAN_SUBSCRIPTION_PRICE0', 0.00),
        'interval'          => env('PLAN_SUBSCRIPTION_INTERVAL0', 'month'),
        'interval_count'    => env('PLAN_SUBSCRIPTION_INTERVAL_COUNT0', 1),
        'google_product_id' => env('PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID0', ''),
        'apple_product_id'  => env('PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID0', ''),
        'free_likes_count'  => env('PLAN_SUBSCRIPTION_FREE_LIKES_COUNT', 30)
    ],

    'plans1' => [
        'name'              => env('PLAN_SUBSCRIPTION_NAME1', 'LoveLock Plus (1 Month)'),
        'plan_code'         => env('PLAN_SUBSCRIPTION_PLAN_CODE1', 'plus_plan_1'),
        'description'       => env('PLAN_SUBSCRIPTION_DESCRIPTION1', 'Pro plan'),
        'price'             => env('PLAN_SUBSCRIPTION_PRICE1', 9.99),
        'interval'          => env('PLAN_SUBSCRIPTION_INTERVAL1', 'month'),
        'interval_count'    => env('PLAN_SUBSCRIPTION_INTERVAL_COUNT1', 1),
        'google_product_id' => env('PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID1', 'lovelock_plus_1_month_test2'),
        'apple_product_id'  => env('PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID1',
            'com.datingAppScript.Lovelock.lovelockplus_001')
    ],

    'plans2' => [
        'name'              => env('PLAN_SUBSCRIPTION_NAME2', 'LoveLock Plus (6 Months)'),
        'plan_code'         => env('PLAN_SUBSCRIPTION_PLAN_CODE2', 'plus_plan_6'),
        'description'       => env('PLAN_SUBSCRIPTION_DESCRIPTION2', 'Plus plan'),
        'price'             => env('PLAN_SUBSCRIPTION_PRICE2', 34.99),
        'interval'          => env('PLAN_SUBSCRIPTION_INTERVAL2', 'month'),
        'interval_count'    => env('PLAN_SUBSCRIPTION_INTERVAL_COUNT2', 6),
        'google_product_id' => env('PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID2', 'lovelock_plus_6_month_test2'),
        'apple_product_id'  => env('PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID2',
            'com.datingAppScript.Lovelock.lovelockplus_002')
    ],

    'plans3' => [
        'name'              => env('PLAN_SUBSCRIPTION_NAME3', 'LoveLock Plus (12 Months)'),
        'plan_code'         => env('PLAN_SUBSCRIPTION_PLAN_CODE3', 'plus_plan_12'),
        'description'       => env('PLAN_SUBSCRIPTION_DESCRIPTION3', 'Plus plan'),
        'price'             => env('PLAN_SUBSCRIPTION_PRICE3', 54.99),
        'interval'          => env('PLAN_SUBSCRIPTION_INTERVAL3', 'year'),
        'interval_count'    => env('PLAN_SUBSCRIPTION_INTERVAL_COUNT3', 1),
        'google_product_id' => env('PLAN_SUBSCRIPTION_GOOGLE_PRODUCT_ID3', 'lovelock_plus_12_month_test2'),
        'apple_product_id'  => env('PLAN_SUBSCRIPTION_APPLE_PRODUCT_ID3',
            'com.datingAppScript.Lovelock.lovelockplus_003')
    ]

];