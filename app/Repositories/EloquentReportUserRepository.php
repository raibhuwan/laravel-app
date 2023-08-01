<?php

namespace App\Repositories;

use App\Repositories\Contracts\ReportUserRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;

class EloquentReportUserRepository extends AbstractEloquentRepository implements ReportUserRepository
{
}