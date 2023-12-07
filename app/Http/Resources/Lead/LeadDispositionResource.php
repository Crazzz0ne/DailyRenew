<?php


namespace App\Http\Resources\Lead;

use Illuminate\Http\Resources\Json\JsonResource;


class LeadDispositionResource extends JsonResource
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
            'lead_id' => $this->lead_id,
            'status' => $this->stage,
            'reason' => $this->reason,
        ];
    }

}
