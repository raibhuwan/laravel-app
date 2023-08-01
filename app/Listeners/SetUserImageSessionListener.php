<?php

namespace App\Listeners;

use App\Events\SetUserImageSessionEvent;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

class SetUserImageSessionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SetUserImageSessionEvent $event
     *
     * @return void
     */
    public function handle($event)
    {
        $image       = Image::where('user_id', Auth::id())->where('number', '1')->first();

        if ($image instanceof Image) {
            $pic = ($image->link == 1) ? $image->path : url('/') . '/storage/' . $image->path . $image->name;

            if ( ! session()->has('profilePic')) {
                session(['profilePic' => $pic]);
            }
            // Updates session if user details are changed
            if (session('profilePic') != $pic) {
                session(['profilePic' => $pic]);
            }
        }
    }
}
