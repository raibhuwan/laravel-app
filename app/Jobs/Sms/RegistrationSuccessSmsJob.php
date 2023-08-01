<?php

namespace App\Jobs;

use App\Helpers\HelperFunctions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RegistrationSuccessSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $message;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /** Create a new job instance.
     * RegistrationSuccessSmsJob constructor.
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
        return trans('sms.registration.successfulRegistration').' '.trans('sms.sender');
    }
}

