<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CallTransformer extends TransformerAbstract
{
    public function transform($call)
    {
        $formattedCall = [

            'session_id'        => $call->session_id,
            'token'             => $call->token,
            'recipient_user_id' => $call->recipient_user_id
        ];

        return $formattedCall;
    }
}