<?php

namespace App\Http\Resources;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadRoofResource extends JsonResource
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
            'age' => $this->age,
            'type' => $this->roof_type_id
        ];
    }
}

