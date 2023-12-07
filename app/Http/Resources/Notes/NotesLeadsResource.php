<?php

namespace App\Http\Resources\Notes;

use Illuminate\Http\Resources\Json\JsonResource;

class NotesLeadsResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->user->id === 1) {
            $user = ['id' => 1, 'fullName' => 'Scout', 'gravatar' => '/img/backend/avatars/avatar.jpg'];
        } else {
            $user = ['id' => $this->user->id, 'fullName' => $this->user->fullName, 'gravatar' => $this->user->picture];
        }

        return [
            'id' => $this->id,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'user' => $user,
            'lead_id' => $this->lead_id,
            'customer' => [
                $this->lead->customer->id,
                $this->lead->customer->fullName
            ]
        ];
    }

}
