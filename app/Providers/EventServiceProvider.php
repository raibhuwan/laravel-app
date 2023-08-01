<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event'                    => [
            'App\Listeners\EventListener',
        ],
        'App\Events\SetUserSessionEvent'      => [
            'App\Listeners\SetUserSessionListener',
        ],
        'App\Events\SetUserImageSessionEvent' => [
            'App\Listeners\SetUserImageSessionListener',
        ],
        'Illuminate\Auth\Events\Login'        => [
            'App\Listeners\SetUserSessionListener',
            'App\Listeners\SetUserImageSessionListener',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Apple\\AppleExtendSocialite@handle',
        ],
    ];
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\UserEventsListener',
        'App\Listeners\PasswordResetEventsListener',
        'App\Listeners\UserCreatedFacebookEventsListener',
        'App\Listeners\GoogleInAppSubscriptionEventsListener',
        'App\Listeners\AppleInAppSubscriptionEventsListener',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
