<?php

namespace App\Transformers;

use App\Models\EmailVerification;
use League\Fractal\TransformerAbstract;

class EmailVerificationTransformer extends TransformerAbstract
{
    public function transform(EmailVerification $email)
    {
        $formattedEmailVerification = [
            'id'         => $email->uid,
            'user_id'    => $email->user_id,
            'email'      => $email->email,
            'verified'   => $email->verified,
            'expired_at' => (string)$email->expired_at,
            'created_at' => (string)$email->created_at,
            'updated_at' => (string)$email->updated_at,
            'deleted_at' => (string)$email->deleted_at
        ];

        return $formattedEmailVerification;
    }
}