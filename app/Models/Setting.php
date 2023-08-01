<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'uid',
        'user_id',
        'search_distance',
        'distance_in',
        'show_ages_min',
        'show_ages_max',
        'interested_in',
        'date_with',
        'privacy_show_distance',
        'privacy_show_age'
    ];

    /**
     * An Setting belongs to user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
