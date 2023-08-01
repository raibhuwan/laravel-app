<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SwipeUserFullDetailsTransformer extends TransformerAbstract
{
    public function transform($user)
    {
        $formattedSwipeUserFullDetails = [
            'user_id'       => $user->user_uid,
            'user_name'     => $user->user_name,
            'user_gender'   => $user->user_gender,
            'user_dob'      => (string)$user->user_dob,
            'user_age'      => (string)$user->user_age,
            'user_about_me' => (string)$user->user_about_me,
            'user_school'   => (string)$user->user_school,
            'user_work'     => (string)$user->user_work,
            'images'        => $user->images,
            'sounds'        => $user->sounds
        ];

        return $formattedSwipeUserFullDetails;
    }
}