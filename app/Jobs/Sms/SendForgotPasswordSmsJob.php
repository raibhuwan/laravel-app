<?php

namespace App\Jobs\Sms;

use App\Helpers\HelperFunctions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendForgotPasswordSmsJob implements ShouldQueue
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

    /**
     * SendForgotPasswordSmsJob constructor.
     *
     * @param $user
     * @param $message
     */
    public function __construct($user, $message)
    {
        $this->user    = $user;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        HelperFunctions::sendSmsMessage($this->user, $this->message);
    }
}
