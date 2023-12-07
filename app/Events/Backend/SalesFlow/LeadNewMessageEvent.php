<?php


namespace App\Events\Backend\SalesFlow;

use App\Http\Resources\LeadNotesResource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use Creativeorange\Gravatar\Facades\Gravatar;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LeadNewMessageEvent
{
    use Dispatchable, SerializesModels;

    public $message;
    public $leadId;
    public $firstName;
    public $lastName;
    public $id;
    public $gravatar;
    public $payload;
    public $lead;
    public $urgent;

    public function __construct($leadId, $noteId, $message, $userId, $urgent = null)
    {
        $lead = Lead::where('id', '=', $leadId)->first();
        $this->lead = $lead;

        if(isset($urgent))
            $this->urgent = $urgent;

        $user = User::where('id',  $userId)->first();

        $firstName = $user->first_name;
        $lastName = $user->last_name;

        $this->leadId = $leadId;
        $this->id = $noteId;
        $this->message = $message;
        $this->gravatar = Gravatar::get($user->email);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->payload = new LeadNotesResource(LeadNote::where('id', '=', $noteId)->first());
    }
}
