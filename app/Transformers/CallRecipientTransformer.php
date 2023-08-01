<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CallRecipientTransformer extends TransformerAbstract
{
    public function transform($call)
    {
        $formattedCallRecipient = [
            'recipient_user_id' => $call->recipient_user_id,
            'user_creator_id'   => $call->user_creator_id,
            'status'            => $call->status,
        ];

        return $formattedCallRecipient;
    }
}
