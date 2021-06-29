<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PwdRecoveryEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = 'geomon.iot@gmail.com';
        $subject = 'Password reset - Geomon IoT';
        $name = 'Geomon';

        return $this->view('mail')
                    ->from($address, $name)
                    //->cc($address, $name)
                    //->bcc($address, $name)
                    //->replyTo($address, $name)
                    ->subject($subject)
                    ->with([ 'password' => $this->data['password'] ]);
    }
}
