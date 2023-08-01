<?php

namespace App\Transformers;

use App\Models\RightLeftSwipe;
use League\Fractal\TransformerAbstract;

class RightLeftSwipeTransformer extends TransformerAbstract
{
    public function transform($rightLeftSwipe)
    {
        $formattedRightLeftSwipe = [
            'swiped_user_id'         => $rightLeftSwipe->swiped_user_id,
            'swiped_user_swipe_type' => $rightLeftSwipe->swiped_user_swipe_type,
            'your_swipe_type'        => $rightLeftSwipe->your_swipe_type,
            'feature'                => $rightLeftSwipe->feature
        ];

        return $formattedRightLeftSwipe;
    }
}