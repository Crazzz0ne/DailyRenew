<?php

namespace App\Mail\SalesFlow;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BaseMailable extends Mailable
{
    use Queueable, SerializesModels;

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
                ->attach($this->file);
        }else {
            return $this->view('mail.basemail');
        }
        return $this->view('view.name');
    }
}
