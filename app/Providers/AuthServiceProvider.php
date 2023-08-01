<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model'           => 'App\Policies\ModelPolicy',
        'App\Models\User'     => 'App\Policies\UserPolicy',
        'App\Models\Image'    => 'App\Policies\ImagePolicy',
        'App\Models\Setting'  => 'App\Policies\SettingPolicy',
        'App\Models\Location' => 'App\Policies\LocationPolicy',
        'App\Models\Video'    => 'App\Policies\VideoPolicy',
        'App\Models\Sound'    => 'App\Policies\SoundPolicy'
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        Passport::tokensCan([
            'admin'        => 'Admin user scope',
            'basic'        => 'Basic user scope',
            'users'        => 'Users scope (perform all actions)',
            'users:list'   => 'Users scope',
            'users:read'   => 'Users scope for reading records',
            'users:write'  => 'Users scope for writing records',
            'users:create' => 'Users scope for creating records',
            'users:delete' => 'Users scope for deleting records',
        ]);
        //
    }
}
