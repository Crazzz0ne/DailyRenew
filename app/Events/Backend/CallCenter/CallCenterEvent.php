<?php

namespace App\Events\Backend\CallCenter;

use App\Models\Auth\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class CallCenterEvent
{
    use Dispatchable, SerializesModels;

    public $readyMode;
    public $propStream;


    /**
     * NewQueueEvent constructor.
     * @param $readyMode
     * @param $propStream
     * @param null $location
     * @param null $user
     */
    public function __construct($readyMode, $propStream)
    {

        $this->readyMode = $readyMode;
        $this->propStream = $propStream;

    }
}
