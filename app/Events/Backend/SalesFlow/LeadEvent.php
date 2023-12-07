<?php


namespace App\Events\Backend\SalesFlow;


use App\Http\Resources\LeadsResource;
use App\Models\SalesFlow\Lead\Lead;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $lead;

    public function __construct($lead)
    {
//        $this->leadId = $leadId;

//        $editing = Lead::where('id', '=', $leadId)->get()->first()->editing;
//        $lead = Lead::find($leadId);

        if ($lead->editing) {
            $this->lead = new LeadsResource(Lead::where('id', '=', $lead->id)
                ->with('reps', 'leadNote', 'user', 'userEdit', 'utility', 'proposal', 'system', 'salesPacket', 'leadUploads',
                    'proposedSystems')
                ->first());
        } else {
            $this->lead = new LeadsResource(Lead::where('id', '=', $lead->id)
                ->with('reps', 'leadNote', 'user', 'utility', 'proposal', 'system', 'salesPacket', 'proposedSystems')
                ->first());
        }
        $this->dontBroadcastToCurrentUser();

    }

    public function broadcastOn()
    {
        return new PrivateChannel('lead.' . $this->lead->id);
    }
}
