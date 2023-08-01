<?php

namespace App\Transformers;

use App\Models\Image;
use League\Fractal\TransformerAbstract;

class ImageTransformer extends TransformerAbstract
{
    public function transform(Image $image)
    {
        $formattedImage = [
            'id'         => $image->uid,
            'user_id'    => $image->user_id,
            'name'       => $image->name,
            'path'       => ($image->link == 1) ? $image->path : url('/') . '/storage/' . $image->path . $image->name,
            'number'     => (int) $image->number,
            'created_at' => (string)$image->created_at,
            'updated_at' => (string)$image->updated_at,
            'deleted_at' => (string)$image->deleted_at
        ];

        return $formattedImage;
    }
}