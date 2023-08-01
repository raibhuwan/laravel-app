<?php

namespace App\Listeners;

use App\Events\UserCreatedFacebookEvent;
use App\Helpers\FacebookHelperFunctions;
use App\Helpers\HelperFunctions;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\SettingController;
use App\Transformers\SettingTransformer;
use Facebook\Facebook;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedFacebookEventsListener
{
    private $user;
    private $profileImage;
    private $permission;

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
     * @param  UserCreatedFacebookEvent $event
     *
     * @return void
     */
    public function handle(UserCreatedFacebookEvent $event)
    {

    }

    /**
     *  Register the listeners for the subscriber.
     *
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(UserCreatedFacebookEvent::class,
            'App\Listeners\UserCreatedFacebookEventsListener@onUserCreatedEvent');
    }

    public function onUserCreatedEvent($event)
    {
        $this->user         = $event->user;
        $this->profileImage = $event->profileImage;

        /**
         * Add default setting for a user
         */
        $defaultSettingObj = new SettingController();
        $defaultSettingObj->insertDefaultSettingFacebook($this->user);

        /**
         * Insert 4 profile pictures
         */
        $imageObj = new ImageController();
        $imageObj->insertOtherProfileImagesFacebook($this->user);
    }
}
