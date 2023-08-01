<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\LocationRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;

class EloquentPlanRepository extends AbstractEloquentRepository implements PlanRepository
{

}