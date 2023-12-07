<?php

namespace App\Events\Backend\SalesFlow\Lead;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JeopardyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;

    public function __construct($lead)
    {
        $this->lead = $lead;
    }

}
