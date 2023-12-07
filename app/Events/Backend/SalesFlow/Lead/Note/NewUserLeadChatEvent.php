<?php

namespace App\Events\Backend\SalesFlow\Lead\Note;


use App\Http\Resources\Notes\NotesLeadsResource;
use App\Models\Auth\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUserLeadChatEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels, InteractsWithSockets;


    public $payload;
    private $user;

    public function __construct($note, User $user)
    {

//        $payload = [];
        $this->user = $user;
//        $payload['id'] = $note->id;
//        $payload['note'] = $note->note;
//        $payload['createdAt'] = $note->created_at;
//        $payload['leadId'] = $note->lead_id;
//
//        $payload['customerName'] = $note->lead->customer->full_name;
//        $payload['user']['fullName'] = $note->user->full_name;
//        $payload['user']['gravatar'] = $note->user->picture;
//        $payload['user']['id'] = $note->user->id;
        $this->payload = new NotesLeadsResource($note);
        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.Models.Auth.User.' . $this->user->id );
    }
}
