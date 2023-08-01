<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetApi extends Model
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
        'expired_at',
    ];

    protected $dates = ['expired_at'];

}
