<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SwipeMatchTransformer extends TransformerAbstract
{
    public function transform($match)
    {
        $formattedSwipeMatch = [
            'user_id'           => $match->user_uid,
            'user_name'         => $match->user_name,
            'image_path'   => (string)($match->image_link == 1) ? $match->image_path : url('/') . '/storage/' . $match->image_path . $match->image_name,
            'matched_date' => $match->swipe_match_created_at
        ];

        return $formattedSwipeMatch;
    }
}