<?php

namespace App\Listeners;

use App\Events\UserEvents\UserCreatedEvent;
use App\Http\Controllers\Api\PhoneVerificationController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\SettingController;
use App\Jobs\RegistrationSuccessSmsJob;

class UserEventsListener
{
    private $user;
    private $setting;
    private $image;

    /**
     * Create the event listener.
     *
     * UserEventsListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreatedEvent $event
     *
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {

    }

    public function onUserCreatedEvent($event)
    {
        $this->user = $event->user;
        $this->setting = $event->setting;
        $this->image = $event->image;

        if (config('smsNotification.registrationSuccess')) {
            dispatch(new RegistrationSuccessSmsJob($this->user));
        }

        $phoneVerificationDelete = new PhoneVerificationController();
        $phoneVerificationDelete->deletePhoneVerificationDetails($this->user);

        /**
         * Add default setting for a user
         */
        $defaultSettingObj = new SettingController();
        $defaultSettingObj->insertDefaultSetting($this->user, $this->setting);

        /**
         * Insert profile image
         */
        $imageObj = new ImageController();
        $imageObj->insertProfileImage($this->user, $this->image);
    }

    /**
     *  Register the listeners for the subscriber.
     *
     * @param $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserCreatedEvent::class,
            'App\Listeners\UserEventsListener@onUserCreatedEvent'
        );

    }
}
