<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * SettingPolicy constructor.
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
     * Determine if a given user can update.
     *
     * @param User $currentUser
     * @param Setting $setting
     *
     * @return bool
     */
    public function update(User $currentUser, Setting $setting)
    {
        return $currentUser->id === $setting->user_id;
    }

    /**
     * Determine if a given user has permission to show
     *
     * @param User $currentUser
     * @param Setting $setting
     *
     * @return bool
     */
    public function show(User $currentUser, Setting $setting)
    {
        return $currentUser->id === $setting->user_id;
    }
}
