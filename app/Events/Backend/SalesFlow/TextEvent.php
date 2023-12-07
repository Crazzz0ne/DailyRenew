<?php

namespace App\Events\Backend\SalesFlow;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TextEvent
{
    use SerializesModels;
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var
     */
    public $too;
    public $body;


    /**
     * @param $too
     * @param $body
     */
    //TODO: Remove and replace down the road.
    public function __construct($too, $body)
    {
        $this->too = $too;
        $this->body = $body;
    }

}
