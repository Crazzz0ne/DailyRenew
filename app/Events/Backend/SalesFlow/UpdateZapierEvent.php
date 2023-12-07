<?php

namespace App\Events\Backend\SalesFlow;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateZapierEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;
    public string $type;

    public function __construct($lead, $type = '')
    {
        $this->lead = $lead;
        $this->type = $type;
    }

}
