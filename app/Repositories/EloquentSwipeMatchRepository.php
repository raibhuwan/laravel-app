<?php

namespace App\Repositories;

use App\Repositories\Contracts\SwipeMatchRepository;
use App\Repositories\Eloquent\AbstractEloquentRepository;
use Illuminate\Support\Facades\DB;

class EloquentSwipeMatchRepository extends AbstractEloquentRepository implements SwipeMatchRepository
{
    public function getMatch($currentUser, $matchedUser)
    {
        // Now we check if the match exists
        $first = DB::table('swipe_matches')->where('a', $currentUser->id)->where('b', $matchedUser->id);

        $query = DB::table('swipe_matches')->where('a', $matchedUser->id)->where('b', $currentUser->id)->union($first);

        $rawQuery = $this->getSql($query);

        $fullQuery = DB::table(DB::raw("(" . $rawQuery . ") as pairs"))->select(DB::raw('count(*) as count'))->get()->first();

        return $fullQuery;

    }
}