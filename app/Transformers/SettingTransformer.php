<?php

namespace App\Transformers;

use App\Models\Setting;
use League\Fractal\TransformerAbstract;

class SettingTransformer extends TransformerAbstract
{
    public function transform(Setting $setting)
    {
        $formattedSetting = [
            'id'                    => $setting->uid,
            'user_id'               => $setting->user_id,
            'search_distance'       => $setting->search_distance,
            'distance_in'           => $setting->distance_in,
            'show_ages_min'         => $setting->show_ages_min,
            'show_ages_max'         => $setting->show_ages_max,
            'interested_in'         => $setting->interested_in,
            'date_with'             => $setting->date_with,
            'privacy_show_distance' => $setting->privacy_show_distance,
            'privacy_show_age'      => $setting->privacy_show_age,
            'created_at'            => (string)$setting->created_at,
            'updated_at'            => (string)$setting->updated_at,
            'deleted_at'            => (string)$setting->deleted_at
        ];

        return $formattedSetting;
    }
}