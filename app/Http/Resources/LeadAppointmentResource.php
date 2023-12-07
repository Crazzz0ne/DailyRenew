<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class LeadAppointmentResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'completed_at' => $this->completed_at,
            'subject' => $this->subject,
            'type' => $this->type->name,
            'type_id' => $this->type_id,
//            'status' => $this->status->name,
            'status_id' => $this->status_id,
            'lead' => new LeadsResource($this->whenLoaded('lead')),
            'lead_id' => $this->lead_id ?? '',
            'remote' => $this->remote,
            'comment' => $this->comment,
            'start_time' => $this->start_time,
            'end_time' => $this->finish_time,
            'created_at' => $this->created_at,
        ];
    }

}
