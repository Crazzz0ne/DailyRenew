<?php


namespace App\Events\Backend\SalesFlow\Lead;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendFileToEPCEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $upload, $lead;

    public function __construct($upload, $lead)
    {
        $this->upload = $upload;
        $this->lead = $lead;
    }
}
