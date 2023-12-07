<?php

namespace App\Mail;


use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaseMultiDomainMailable  extends Mailable
{
    use  SerializesModels;

    public $subject, $link, $body, $file, $linkTitle;

    /**
     * Create a new message instance.
     * @param $subject
     * @param $body
     * @param null $link
     * @param null $linkTitle
     * @param null $file
     */
    public function __construct($subject, $body, $link = null, $linkTitle = null, $file = null)
    {

        $this->subject = $subject;
        $this->body = $body;
        $this->link = $link;
        $this->file = $file;
        $this->linkTitle = $linkTitle;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (isset($this->file)){
            return $this->view('mail.basemail')
                ->attachFromStorage($this->file);
        }else {
            return $this->view('mail.basemail');
        }
    }
}
