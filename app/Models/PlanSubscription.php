<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanSubscription extends Model
{
    protected $fillable = [
        'plan_id',
        'starts_at',
        'ends_at',
        'subscribable_id',
        'subscribable_type',
        'name'
    ];
}
