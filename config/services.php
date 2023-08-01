<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model'  => App\Models\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'twilio' => [
        'accountSid'  => env('TWILIO_ACCOUNT_SID'),
        'authToken'   => env('TWILIO_AUTH_TOKEN'),
        'phoneNumber' => env('TWILIO_PHONE_NUMBER'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('FACEBOOK_REDIRECT'). '/oauth/facebook/callback'
    ],

    "apple" => [
        "redirect" => env("SIGN_IN_WITH_APPLE_REDIRECT"),
        "client_id" => env("SIGN_IN_WITH_APPLE_CLIENT_ID"),
        "client_secret" => env("SIGN_IN_WITH_APPLE_CLIENT_SECRET"),
    ],

];
