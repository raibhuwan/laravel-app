<?php

namespace App\Transformers;

use App\Models\PhoneVerification;
use League\Fractal\TransformerAbstract;

class PhoneVerificationTransformer extends TransformerAbstract
{
    public function transform(PhoneVerification $phone)
    {
        $formattedPhoneVerification = [
            'id'           => $phone->uid,
            'country_code' => $phone->country_code,
            'phone'        => $phone->phone,
            'verified'     => $phone->verified,
            'expired_at'   => (string)$phone->expired_at,
            'created_at'   => (string)$phone->created_at,
            'updated_at'   => (string)$phone->updated_at,
            'deleted_at'   => (string)$phone->deleted_at
        ];

        return $formattedPhoneVerification;
    }
}