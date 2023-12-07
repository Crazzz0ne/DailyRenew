<?php


namespace App\Events\Backend\SalesFlow;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 *  Importing contacts from old isntalls then sending them over to mailchimp to market to them but do not want to hit all
 * 300 at once.
 *
 */

class MailChimpDripAddEvent
{
    use SerializesModels;
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var
     */
    public $email;
    public $firstName;
    public $lastName;


    /**
     * @param $email
     * @param $firstName
     * @param $lastName
     */
    public function __construct($email, $firstName, $lastName)
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;

    }

}
