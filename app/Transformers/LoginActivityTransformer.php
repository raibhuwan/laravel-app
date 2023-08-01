<?php

namespace App\Transformers;

use App\Models\LoginActivity;
use League\Fractal\TransformerAbstract;

class LoginActivityTransformer extends TransformerAbstract
{
    public function transform(LoginActivity $loginActivity)
    {

        $formattedLoginActivity = [
            'id'                  => $loginActivity->uid,
            'user_id'             => $loginActivity->user_id,
            'ip_address'          => $loginActivity->ip_address,
            'user_agent'          => $loginActivity->user_agent,
            'is_logged_in'        => $loginActivity->is_logged_in,
            'fcm_registration_id' => $loginActivity->fcm_registration_id,
            'created_at'          => (string)$loginActivity->created_at,
            'updated_at'          => (string)$loginActivity->updated_at,
            'deleted_at'          => (string)$loginActivity->deleted_at
        ];

        return $formattedLoginActivity;
    }
}
