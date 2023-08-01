<?php

namespace App\Listeners;

use App\Events\Event;
use App\Events\GoogleInAppSubscriptionEvent;
use App\Http\Controllers\Api\GoogleInAppSubscriptionController;
use App\Models\GoogleInAppSubscription;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GoogleInAppSubscriptionEventsListener
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
     * @param  Event $event
     *
     * @return void
     */
    public function handle()
    {

    }

    public function subscribe($events)
    {
        $events->listen(GoogleInAppSubscriptionEvent::class,
            'App\Listeners\GoogleInAppSubscriptionEventsListener@onSubscriptionEvent');
    }

    public function onSubscriptionEvent($event)
    {
        $googleInAppSubscription = new  GoogleInAppSubscriptionController();
        $googleInAppSubscription->store($event->subscription, $event->receipt);

    }
}
