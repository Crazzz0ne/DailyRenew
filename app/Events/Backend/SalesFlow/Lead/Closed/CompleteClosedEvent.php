<?php

namespace App\Events\Backend\SalesFlow\Lead\Closed;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompleteClosedEvent {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Lead $lead;
    public $leadId;

    public function __construct($leadId, Lead $lead)
    {
        $this->leadId = $leadId;
        $this->lead = $lead;


    }
}
