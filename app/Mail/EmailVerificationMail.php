<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $pin;

    /**
     * EmailVerificationMail constructor.
     *
     * @param $pin
     *
     * @internal param $emailVerification
     */
    public function __construct($pin)
    {
        $this->pin = $pin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.sendVerificationCode')
                    ->from(config('mail.from.address'))
                    ->with([
                        'verification_code' => $this->pin,
                    ]);
    }
}
