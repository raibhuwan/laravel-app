<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     * ImagePolicy constructor.
     */
    public function __construct()
    {
        //
    }

    public function before(User $currentUser, $ability)
    {
        //Add tokenCan without * scope. To be sure that basic scope is passed. Now check if * scope is passed with undefined scope
        if ($currentUser->isAdmin() && ( ! $currentUser->tokenCan('basic') || $currentUser->tokenCan('undefined'))) {
            return true;
        }
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param Video $video
     *
     * @return bool
     */
    public function show(User $currentUser, Video $video)
    {
        return $currentUser->id === $video->user_id;
    }

    /**
     * Determine if a given user can update.
     *
     * @param User $currentUser
     * @param Video $video
     *
     * @return bool
     */
    public function update(User $currentUser, Video $video)
    {
        return $currentUser->id === $video->user_id;
    }

}
