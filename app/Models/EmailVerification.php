<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['uid', 'user_id', 'email', 'token', 'expiration_date', 'expired_at', 'verified'];

    protected $dates = ['expired_at'];
}
