<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@welcome');

Route::get('/privacy', 'HomeController@privacy');

Route::get('/tac', 'HomeController@terms');

Route::get('/legal', 'HomeController@legal');

Auth::routes();

Route::prefix(config('backend.admin_backend_url'))->middleware('auth')->group(function () {
    Route::get('/dashboard', 'Backend\DashboardController@index')->name('home');

    Route::get('/user/read', 'Backend\UserController@readAjax');
    Route::resource('user', 'Backend\UserController');

    Route::get('/report/user/read', 'Backend\ReportUserController@readAjax');
    Route::resource('report/user', 'Backend\ReportUserController', [
        'as' => 'report'
    ]);

    Route::get('user/{id}/edit/image', 'Backend\ImageController@edit')->name('image.edit');
    Route::post('user/{id}/edit/image', 'Backend\ImageController@store')->name('image.store');
    Route::post('user/tempImage', 'Backend\ImageController@tempStore')->name('image.tempStore');
    Route::delete('user/{id}/delete/image', 'Backend\ImageController@destroy')->name('image.destroy');

    Route::get('user/{id}/edit/setting', 'Backend\SettingController@edit')->name('setting.edit');
    Route::put('user/{id}/edit/setting', 'Backend\SettingController@update')->name('setting.update');

    Route::get('user/{id}/edit/location', 'Backend\LocationController@edit')->name('location.edit');
    Route::put('user/{id}/edit/location', 'Backend\LocationController@update')->name('location.update');

    Route::get('user/{id}/subscriptions', 'Backend\UserController@subscriptionList')->name('user.subscriptions');
    Route::get('user/{uid}/edit/{sid}/subscription', 'Backend\PlanSubscriptionController@edit')->name('user.subscription.edit');
    Route::put('user/{uid}/subscription/{sid}', 'Backend\PlanSubscriptionController@update')->name('user.subscription.update');
    Route::get('user/{uid}/subscription/{sid}', 'Backend\PlanSubscriptionController@show')->name('user.subscription.show');
    Route::delete('user/subscription/{sid}', 'Backend\PlanSubscriptionController@destroy')->name('user.subscription.destroy');
    Route::get('/readsubscription', 'Backend\PlanSubscriptionController@readSubscription');
    Route::get('user/{uid}/create/subscription', 'Backend\PlanSubscriptionController@create')->name('user.subscription.create');
    Route::post('user/{uid}/subscription', 'Backend\PlanSubscriptionController@store')->name('user.subscription.store');

});

Route::prefix(config('backend.admin_backend_url'))->middleware('auth')->group(function () {

    Route::resource('plan', 'Backend\PlanController');
    Route::get('/readplan', 'Backend\PlanController@readPlan')->name('plan.readplan');

});

Route::get('/viewUserLocationMap', 'Backend\ViewUserLocationMapController@index');

Route::get('/jsonDataOfAllUsers', 'Backend\ViewUserLocationMapController@jsonDataOfAllUsers');

Route::post('/getUserDataForMap', 'Backend\ViewUserLocationMapController@getUserDataForMap');