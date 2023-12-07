<?php

namespace App\Events\Backend\SalesFlow\Commissions;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallCenterBonusEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;



    public function __construct($lead)
    {
        $this->lead = $lead;
    }

}
