<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class AssignSP1Resource extends JsonResource
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
            'lead_id' => $this->lead->id,
            'start_time' => $this->start_time,
            'end_time' => $this->finish_time,
        ];
    }

}
