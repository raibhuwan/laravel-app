<?php

namespace App\Transformers;

use App\Models\PasswordResetApi;
use League\Fractal\TransformerAbstract;

class PasswordResetApiTransformer extends TransformerAbstract
{
    public function transform(PasswordResetApi $password)
    {
        $formattedPasswordResetApi= [
            'uid'          => $password->uid,
            'country_code' => $password->country_code,
            'phone'        => $password->phone,
            'expired_at'   => (string)$password->expired_at,
            'created_at'   => (string)$password->created_at,
            'updated_at'   => (string)$password->updated_at,
        ];

        return $formattedPasswordResetApi;
    }
}