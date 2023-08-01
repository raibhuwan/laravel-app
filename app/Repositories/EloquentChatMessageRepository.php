<?php

namespace App\Repositories;

use App\Repositories\Contracts\ChatMessageRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;


class EloquentChatMessageRepository extends AbstractEloquentRepository implements ChatMessageRepository
{

}