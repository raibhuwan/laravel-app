<?php

namespace App\Jobs\Sms;

use App\Helpers\HelperFunctions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PhoneVerifiedSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;


    /**
     * PhoneVerifiedSmsJob constructor.
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = $this->messageText();
        HelperFunctions::sendSmsMessage($this->user, $message);
    }

    /**
     * Return text message
     *
     * @return string
     */
    private function messageText()
    {
        return trans('sms.registration.phoneVerified') . ' '.trans('sms.sender');
    }
}
