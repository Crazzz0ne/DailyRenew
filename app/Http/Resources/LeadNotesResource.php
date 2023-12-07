<?php

namespace App\Http\Resources;

use App\Models\Auth\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadNotesResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param Request $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
		    'id' => $this->id,
            'note' => $this->note,
            'lead_id' => $this->lead_id,
            'user' => new UserResource(User::find($this->user_id)),
            'customerName' => $this->lead->customer->fullName,
            'lead' => new LeadsResource($this->whenLoaded('lead')),
            'created_at' => $this->created_at
        ];
	}
}
