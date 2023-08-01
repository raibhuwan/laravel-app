<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Positive Words
    |--------------------------------------------------------------------------
    |
    | These words indicates "true" and are used to check if a particular plan
    | feature is enabled.
    |
    */
    'positive_words' => [
        'Y',
        'YES',
        'TRUE',
        'UNLIMITED',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | If you want to use your own models you will want to update the following
    | array to make sure Laraplans use them.
    |
    */
    'models'         => [
        'plan'                    => 'Gerardojbaez\Laraplans\Models\Plan',
        'plan_feature'            => 'Gerardojbaez\Laraplans\Models\PlanFeature',
        'plan_subscription'       => 'Gerardojbaez\Laraplans\Models\PlanSubscription',
        'plan_subscription_usage' => 'Gerardojbaez\Laraplans\Models\PlanSubscriptionUsage',
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | The heart of this package. Here you will specify all features available
    | for your plans.
    |
    */
    'features'       => [
        'LIKE'                  => [
            'resettable_interval' => 'day',
            'resettable_count'    => 1
        ],
        'REWIND_SWIPE'          => [
            'resettable_interval' => 'day',
            'resettable_count'    => 1
        ],
        'PRIVACY_SHOW_DISTANCE' => [
            'resettable_interval' => 'day',
            'resettable_count'    => 1
        ],
        'PRIVACY_SHOW_AGE'      => [
            'resettable_interval' => 'day',
            'resettable_count'    => 1
        ],
        'CUSTOM_LOCATION'       => [
            'resettable_interval' => 'day',
            'resettable_count'    => 1
        ],
        'SUPER_LIKE'            => [
            'resettable_interval' => 'day',
            'resettable_count'    => 1
        ],
        'BOOST_PROFILE'         => [
            'resettable_interval' => 'day',
            'resettable_count'    => 30
        ],
        'TURN_OFF_AD'           => [
            'resettable_interval' => 'day',
            'resettable_count'    => 1
        ]
    ],
];
