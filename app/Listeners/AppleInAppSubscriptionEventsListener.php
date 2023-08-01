<?php

namespace App\Listeners;

use App\Events\AppleInAppSubscriptionEvent;
use App\Http\Controllers\Api\AppleInAppSubscriptionController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppleInAppSubscriptionEventsListener
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
     * @param  AppleInAppSubscriptionEvent  $event
     * @return void
     */
    public function handle(AppleInAppSubscriptionEvent $event)
    {
        //
    }

    public function subscribe($events)
    {
        $events->listen(AppleInAppSubscriptionEvent::class,
            'App\Listeners\AppleInAppSubscriptionEventsListener@onSubscriptionEvent');
    }

    public function onSubscriptionEvent($event)
    {
        $googleInAppSubscription = new  AppleInAppSubscriptionController();
//        $googleInAppSubscription->store($event->subscription, $event->receipt);

    }
}
