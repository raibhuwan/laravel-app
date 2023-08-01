<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SwipeBoostProfileTransformer extends TransformerAbstract
{
    public function transform($user)
    {

        $formattedSwipe = [
            'user_id'                       => $user->user_uid,
            'user_user_id'                  => $user->user_user_id,
            'user_name'                     => $user->user_name,
            'user_gender'                   => $user->user_gender,
            'user_dob'                      => (string)($user->setting_privacy_show_age == 1) ? $user->user_dob : '',
            'user_age'                      => (string)($user->setting_privacy_show_age == 1) ? (string)$user->user_age : '',
            'image_path'                    => (string)($user->image_link == 1) ? $user->image_path : url('/') . '/storage/' . $user->image_path . $user->image_name,
            'setting_interested_in'         => $user->setting_interested_in,
            'setting_date_with'             => $user->setting_date_with,
            'setting_privacy_show_distance' => $user->setting_privacy_show_distance,
            'setting_privacy_show_age'      => $user->setting_privacy_show_age,
            'swipe_type'                    => (string)$user->swipe_type,
            'location_distance'             => (string)($user->setting_privacy_show_distance == 1) ? $user->location_distance : '',
            'boost_profiles_expired_at'     => $user->boost_profiles_expired_at
        ];

        return $formattedSwipe;
    }
}
