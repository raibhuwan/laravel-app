<?php

namespace App\Listeners;

use App\Events\PasswordResetEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class PasswordResetEventsListener
{
    public $user;

    /**
     * PasswordResetEventsListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle()
    {

    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            PasswordResetEvent::class,
            'App\Listeners\PasswordResetEventsListener@onPasswordResetEvent'
        );

    }

    public function onPasswordResetEvent($event)
    {
        // Revoke all tokens after reseting password
        $this->user = $event->user;
        DB::table('oauth_access_tokens')
          ->where('user_id', $this->user->id)
          ->update(['revoked' => true]);


    }
}
