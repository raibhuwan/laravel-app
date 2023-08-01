<?php

namespace App\Repositories;

use App\Repositories\Contracts\PasswordResetsApi;
use App\Repositories\Eloquent\AbstractEloquentRepository;

class EloquentPasswordResetApiRepository extends AbstractEloquentRepository implements PasswordResetsApi
{

}