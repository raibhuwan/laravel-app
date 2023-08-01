<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class BoostProfileTransformer extends TransformerAbstract
{
    public function transform($boostProfile)
    {
        $formattedBoostProfile = [
            'status'  => $boostProfile->status,
            'feature' => $boostProfile->feature
        ];

        return $formattedBoostProfile;
    }
}