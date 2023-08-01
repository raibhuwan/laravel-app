<?php

namespace App\Transformers;

use App\Models\RightLeftSwipe;
use League\Fractal\TransformerAbstract;

class RewindSwipeTransformer extends TransformerAbstract
{
    public function transform($rewindSwipe)
    {
        $formattedRewindSwipe = [
            'user_id'    => $rewindSwipe->user_id,
            'swipe_type' => $rewindSwipe->swipe_type,
            'feature'    => $rewindSwipe->feature
        ];

        return $formattedRewindSwipe;
    }
}
