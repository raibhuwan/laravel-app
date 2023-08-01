<?php

namespace App\Repositories;

use App\Repositories\Contracts\EmailVerificationRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;


class EloquentEmailVerificationRepository extends AbstractEloquentRepository implements EmailVerificationRepository
{

}