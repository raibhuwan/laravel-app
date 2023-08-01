<?php

namespace App\Providers;

use App\Http\Controllers\Api\FacebookLoginRequestGrant;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Bridge\UserRepository;
use League\OAuth2\Server\AuthorizationServer;

class FacebookLoginGrantProvider extends ServiceProvider
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
            __DIR__.'/config/facebook.php' => config_path('facebook.php'),
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
     * @return FacebookLoginRequestGrant
     */
    protected function makeRequestGrant()
    {
        $grant = new FacebookLoginRequestGrant($this->app->make(UserRepository::class),
            $this->app->make(RefreshTokenRepository::class));

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
