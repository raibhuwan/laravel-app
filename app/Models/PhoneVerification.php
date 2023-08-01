<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'country_code',
        'phone',
        'token',
        'expiration_date',
        'expired_at',
        'verified'
    ];

    protected $dates = ['expired_at'];
}
