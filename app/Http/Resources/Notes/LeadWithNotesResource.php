<?php

namespace App\Http\Resources\Notes;


use Illuminate\Http\Resources\Json\JsonResource;

class LeadWithNotesResource  extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'latestNoteID' => $this->notes[0]->id,
            'notes' => NotesLeadsResource::collection($this->notes),
            'status' => $this->statusName->name?? '',
            'created_at' => $this->created_at,
            'customer' => [
                'id' => $this->customer->id,
                'fullName' => $this->customer->fullName
            ],
        ];
    }

}
