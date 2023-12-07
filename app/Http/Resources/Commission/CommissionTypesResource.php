<?php


namespace App\Http\Resources\Commission;


use App\Http\Resources\UserResource;
use App\Models\Commission\CommissionLedgers;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionTypesResource extends JsonResource
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
            'value' => $this->id,
            'label' => $this->name
        ];
    }

}
