<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoostProfile extends Model
{
    protected $fillable = [
        'uid',
        'user_id',
        'expired_at'
    ];
}
