<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RightLeftSwipe extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'a',
        'b',
        'swipe_type',
        'expired_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
//        'swipe_record' => 'array',
    ];
}
