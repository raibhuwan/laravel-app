<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class FeatureTransformer extends TransformerAbstract
{
    public function transform($feature)
    {

        $formattedFeature = [
            'feature' => $feature->feature
        ];

        return $formattedFeature;
    }
}