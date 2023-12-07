<?php

namespace App\Mail\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangeMailable  extends Mailable
{
    use Queueable, SerializesModels;
    public $newEmail, $firstName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($newEmail, $firstName)
    {
        $this->newEmail = $newEmail;
        $this->firstName = $firstName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $newEmail = $this->newEmail;
        $firstName = $this->firstName;
        $this->subject('New Email Please Read')->from('chris.furman@solcalenergy.com')->bcc('chris.furman@solcalenergy.com');

        return $this->view('mail.support.newEmail', compact('newEmail', 'firstName'));
    }
}
