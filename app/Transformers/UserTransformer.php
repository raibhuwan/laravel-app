<?php

namespace App\Transformers;

use App\Models\User;
use function GuzzleHttp\Psr7\str;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {

        $formattedUser = [
            'id'             => $user->uid,
            'name'           => $user->name,
            'country_code'   => (string)$user->country_code,
            'phone'          => (string)$user->phone,
            'phone_verified' => (integer)$user->phone_verified,
            'email'          => (string)$user->email,
            'email_verified' => (integer)$user->email_verified,
            'gender'         => $user->gender,
            'dob'            => $user->dob,
            'role'           => $user->role,
            'about_me'       => (string)$user->about_me,
            'work'           => (string)$user->work,
            'school'         => (string)$user->school,
            'is_active'      => $user->is_active,

            'provider'    => (string)$user->provider,
            'provider_id' => (string)$user->provider_id,

            'created_at' => (string)$user->created_at,
            'updated_at' => (string)$user->updated_at,
            'deleted_at' => (string)$user->deleted_at
        ];


        return $formattedUser;
    }
}