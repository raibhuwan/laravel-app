<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sound;
use Illuminate\Auth\Access\HandlesAuthorization;

class SoundPolicy
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
     * @param Sound $sound
     *
     * @return bool
     */
    public function show(User $currentUser, Sound $sound)
    {
        return $currentUser->id === $sound->user_id;
    }

    /**
     * Determine if a given user can update.
     *
     * @param User $currentUser
     * @param Sound $sound
     *
     * @return bool
     */
    public function update(User $currentUser, Sound $sound)
    {
        return $currentUser->id === $sound->user_id;
    }

    /**
     * Determine if a given user can delete
     *
     * @param User $currentUser
     * @param Sound $sound
     *
     * @return bool
     */
    public function destroy(User $currentUser, Sound $sound)
    {
        return $currentUser->id === $sound->user_id;
    }

}
