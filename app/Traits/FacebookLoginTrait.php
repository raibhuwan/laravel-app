<?php

namespace App\Traits;

use App\Http\Controllers\Api\Auth\FacebookAuthController;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuthServerException;

trait FacebookLoginTrait
{
    public function loginFacebook(Request $request)
    {
        if ( ! $request->get('fb_token')) {
            throw OAuthServerException::serverError('fb_token field is required.');
        }

        $socialAuth = new FacebookAuthController();
        $user       = $socialAuth->loginOrCreateAccount($request);

        return $user;
    }
}