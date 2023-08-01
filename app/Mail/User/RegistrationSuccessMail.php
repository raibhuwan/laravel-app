<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegistrationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The registration success instance.
     *
     * @var Order
     */
    protected $registrationSuccess;

    /**
     * RegistrationSuccessEmailJob constructor.
     *
     * @param $registrationSuccess
     *
     */
    public function __construct($registrationSuccess)
    {
        $this->registrationSuccess = $registrationSuccess;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.user.registrationSuccess')
                    ->from(config('mail.from.address'));
    }
}
