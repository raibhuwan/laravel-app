<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', 'UserController@AuthRouteAPI');

Route::group(['middleware' => 'throttle:60,1'], function () {
    // Google Pubsub
    Route::post('pubsub', 'Api\SubscriptionController@pubSubRequest');


    //  Authentication Routes...
    Route::post('accessToken', 'Api\AccessTokenController@createAccessToken');

    // Receive timestamp...
    Route::get('time/now', 'Api\TimeController@index');

    //  Registration Routes...
    Route::post('users', 'Api\UserController@store');

    //  Verify Phone number Routes...
    Route::post('phone/sendCode', 'Api\PhoneVerificationController@store');
    Route::post('phone/verifyCode', 'Api\PhoneVerificationController@verifyPhone');

    //  Password Reset Routes...
    Route::post('password/phone', 'Api\Auth\ForgotPasswordController@sendResetCodePhone');
    Route::post('password/reset', 'Api\Auth\ResetPasswordController@reset');

    // Used by ios
    Route::get('plans', [
        'uses' => 'Api\PlanController@index'
    ]);

});

Route::group(['middleware' => 'auth:api'], function () {
    // Report User
    Route::post('report/user', 'Api\ReportUserController@store');
    //Swipe
    Route::get('swipe/getUser', 'Api\SwipeController@getUser');
    //Get swipe user full details
    Route::get('swipe/getUser/{id}', 'Api\SwipeController@getUserFullDetails');
    // Right Left Swipe
    Route::post('swipe/rightLeft', 'Api\RightLeftSwipeController@store');
    // Rewind Swipe
    Route::post('swipe/rewindSwipe', 'Api\RewindSwipeController@store');

    // Get Match
    Route::get('swipe/match', 'Api\SwipeMatchController@getMatchUser');
    // Store Match
    Route::post('swipe/match', 'Api\SwipeMatchController@store');
    // Delete match (Keep Swiping)
    Route::delete('swipe/match/{id}', 'Api\SwipeMatchController@destroy');

    // FCM
    Route::post('fcm/updateFcmID', 'Api\MessageController@updateFcmID');
    // Sending push notification to a single user
    Route::post('fcm/users/message', 'Api\MessageController@messageSingleUser');
    // Get message
    Route::get('fcm/message/{id}', 'Api\MessageController@getMessage');

    // Create Subscription
    Route::post('subscription', 'Api\SubscriptionController@store');
    // Renew Subscription
    Route::post('subscription/renew', 'Api\SubscriptionController@renewTest');
    // Delete Subscription
    Route::post('subscription/cancel', 'Api\SubscriptionController@cancelTest');

    // Resume Subscriptions
    Route::post('subscription/resume', 'Api\SubscriptionController@resumeTest');
    Route::get('subscription/canSubscribeToOtherPlan', 'Api\SubscriptionController@canSubscribeToOtherPlan');

    //Test subscription
    Route::post('subscription/test', 'Api\SubscriptionController@testSubscribe');

    //Feature
    Route::get('feature', 'Api\FeatureController@getFeatures');

    //  Verify Email Routes...
    Route::post('email/sendCode', 'Api\EmailVerificationController@store');
    Route::post('email/verifyCode', 'Api\EmailVerificationController@verifyEmail');

    //  Change Phone Routes...
    Route::post('phone/change', [
        'uses' => 'Api\PhoneVerificationController@changePhone'
    ]);

    //  Logout Routes...
    Route::get('logout', 'Api\AccessTokenController@logout');

    Route::get('users', [
        'uses'       => 'Api\UserController@index',
        'middleware' => "scope:users,users:list"
    ]);

    Route::get('users/details', [
        'uses' => 'Api\UserController@getDetailUser',
//        'middleware' => "scope:users,users:read"
    ]);

    Route::get('users/{email}', [
        'uses'       => 'Api\UserController@show',
        'middleware' => "scope:users,users:read"
    ]);

    Route::put('users/{id}', [
        'uses' => 'Api\UserController@update',
//        'middleware' => "scope:users,users:write"
    ]);

    Route::post('users/password/change', 'Api\UserController@changePassword');

    Route::delete('users/email/delete', 'Api\UserController@deleteEmail');

    //deleting the user
    Route::delete('/users', 'Api\UserController@destroy');

    Route::post('images', [
        'as'   => 'postimage',
        'uses' => 'Api\ImageController@store'
    ]);

    Route::get('images', [
        'uses' => 'Api\ImageController@index'
    ]);

    Route::get('images/user', [
        'uses' => 'Api\ImageController@getImagesUser'
    ]);

    Route::delete('images', [
        'uses' => 'Api\ImageController@delete'
    ]);

    Route::get('settings', [
        'uses' => 'Api\SettingController@index'
    ]);

    Route::get('settings/user', [
        'uses' => 'Api\SettingController@getSettingUser'
    ]);

    Route::get('settings/{id}', [
        'uses' => 'Api\SettingController@show'
    ]);

    Route::post('settings', [
        'uses' => 'Api\SettingController@store'
    ]);

    Route::put('settings/{id}', [
        'uses' => 'Api\SettingController@update'
    ]);

    Route::get('locations', [
        'uses' => 'Api\LocationController@index'
    ]);

    Route::get('locations/user', [
        'uses' => 'Api\LocationController@getLocationUser'
    ]);

    Route::get('locations/{id}', [
        'uses' => 'Api\LocationController@show'
    ]);

    Route::post('locations', [
        'uses' => 'Api\LocationController@store'
    ]);

    Route::put('locations/{id}', [
        'uses' => 'Api\LocationController@update'
    ]);

    //For Video
    Route::get('videos', [
        'uses' => 'Api\VideoController@index'
    ]);

    Route::get('videos/user', [
        'uses' => 'Api\VideoController@getVideoUser'
    ]);

    Route::get('videos/{id}', [
        'uses' => 'Api\VideoController@show'
    ]);

    Route::post('videos', [
        'uses' => 'Api\VideoController@store'
    ]);

    Route::put('videos/{id}', [
        'uses' => 'Api\VideoController@update'
    ]);

    Route::delete('videos/{id}', [
        'uses' => 'Api\VideoController@destroy'
    ]);

    //For Sound
    Route::get('sounds', [
        'uses' => 'Api\SoundController@index'
    ]);

    Route::get('sounds/user', [
        'uses' => 'Api\SoundController@getSoundUser'
    ]);

    Route::get('sounds/{id}', [
        'uses' => 'Api\SoundController@show'
    ]);

    Route::post('sounds', [
        'uses' => 'Api\SoundController@store'
    ]);

    Route::put('sounds/{id}', [
        'uses' => 'Api\SoundController@update'
    ]);

    Route::delete('sounds/{id}', [
        'uses' => 'Api\SoundController@destroy'
    ]);

    Route::post('calls/video', [
        'uses' => 'Api\CallController@requestCall'
    ]);

    Route::post('calls/respond', [
        'uses' => 'Api\CallController@respond'
    ]);

    Route::post('boostProfile', 'Api\BoostProfileController@store');
    Route::get('boostProfile', 'Api\BoostProfileController@index');

});

