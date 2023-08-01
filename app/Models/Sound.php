<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sound extends Model
{
    //    use SoftDeletes;

    protected $fillable = [
        'uid',
        'name',
        'user_id',
        'name',
        'path'
    ];

    /**
     * An video belongs to user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
