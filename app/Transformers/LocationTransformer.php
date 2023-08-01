<?php

namespace App\Transformers;

use App\Models\Image;
use App\Models\Location;
use League\Fractal\TransformerAbstract;

class LocationTransformer extends TransformerAbstract
{
    public function transform(Location $location)
    {

        $formattedLocation = [
            'id'         => $location->uid,
            'user_id'    => $location->user_id,
            'latitude'   => $location->latitude,
            'longitude'  => $location->longitude,
            'created_at' => (string)$location->created_at,
            'updated_at' => (string)$location->updated_at,
            'deleted_at' => (string)$location->deleted_at
        ];

        return $formattedLocation;
    }
}