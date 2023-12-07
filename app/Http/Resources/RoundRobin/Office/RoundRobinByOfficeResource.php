<?php


namespace App\Http\Resources\RoundRobin\Office;


use Illuminate\Http\Resources\Json\JsonResource;

class RoundRobinByOfficeResource  extends JsonResource
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
            'name' => $this->lead->id,
//            'customer' => new ($this->whenLoaded('roundRobin')),
        ];
    }

}
