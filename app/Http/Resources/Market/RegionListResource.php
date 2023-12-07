<?php


namespace App\Http\Resources\Market;


use Illuminate\Http\Resources\Json\JsonResource;

class RegionListResource extends JsonResource
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
            'label' => $this->name,
            'value' => $this->id,
        ];
    }
}
