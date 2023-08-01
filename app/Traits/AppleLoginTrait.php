<?php

namespace App\Traits;

use App\Http\Controllers\Api\Auth\AppleAuthController;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;

trait AppleLoginTrait
{
    public function loginApple(Request $request)
    {
        if ( ! $request->get('apple_token')) {
            throw OAuthServerException::serverError('apple_token field is required.');
        }

        $socialAuth = new AppleAuthController();
        $user       = $socialAuth->loginOrCreateAccount($request);

        return $user;
    }
}