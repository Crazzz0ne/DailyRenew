<?php


namespace App\Mail\SalesFlow;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DIdNotCloseMailable  extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $customer, $powerCompany;


    public function __construct($subject, $customer, $powerCompany )
    {
        $this->subject = $subject;
        $this->customer = $customer;
        $this->powerCompany = $powerCompany;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

//        if (isset($this->file)){
//            return $this->view('mail.basemail')
//                ->attachFromStorage($this->file);
//        }else {
//            return $this->view('mail.basemail');
//        }
        return $this->from('gary@solcalenergy.com')->view('mail.follow-up.did-not-close');
    }
}
