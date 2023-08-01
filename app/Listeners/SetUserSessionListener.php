<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Auth;

class SetUserSessionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * @param $event
     */
    public function handle($event)
    {
        if (Auth::user()->id == $event->user->id) {
            if ( ! session()->has('currentUser')) {
                // Save it to session
                session(['currentUser' => $event->user]);

                return;
            }
                // Updates session if user details are changed
            if (session('currentUser') != $event->user) {
                session(['currentUser' => $event->user]);
            }
        }

        return;
    }

}
