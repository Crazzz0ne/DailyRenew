<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoofTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        $snakeCase = strtolower(str_replace(" ", "-", $this->name));
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name,
            'value' => $this->id,
        ];
    }
}
