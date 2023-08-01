<?php

namespace App\Transformers;

use App\Models\Sound;
use League\Fractal\TransformerAbstract;

class SoundTransformer extends TransformerAbstract
{
    public function transform(Sound $sound)
    {
        $formattedSound = [
            'id'         => $sound->uid,
            'user_id'    => $sound->user_id,
            'name'       => $sound->name,
            'path'       => url('/') . '/storage/' . $sound->path . $sound->name,
            'created_at' => (string)$sound->created_at,
            'updated_at' => (string)$sound->updated_at,
            'deleted_at' => (string)$sound->deleted_at
        ];

        return $formattedSound;
    }
}