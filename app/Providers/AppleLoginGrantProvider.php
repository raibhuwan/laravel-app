<?php

namespace App\Providers;

use App\Http\Controllers\Api\AppleLoginRequestGrant;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\UserRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;

class AppleLoginGrantProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Create our facebook.php configuration file.
         */
        $this->publishes([
            __DIR__.'/config/apple.php' => config_path('apple.php'),
        ]);
        if (file_exists(storage_path('oauth-private.key'))) {
            app(AuthorizationServer::class)->enableGrantType($this->makeRequestGrant(), Passport::tokensExpireIn());
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return AppleLoginRequestGrant
     */
    protected function makeRequestGrant()
    {
        $grant = new AppleLoginRequestGrant($this->app->make(UserRepository::class),
            $this->app->make(RefreshTokenRepository::class));

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
