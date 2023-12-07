<?php


namespace App\Http\Resources\Epc;


use Illuminate\Http\Resources\Json\JsonResource;

class EpcEquipmentResource  extends JsonResource
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
            'name' => $this->name,
            'cost' => $this->cost,
            'type' => $this->type,
            'epc_id' => $this->epc_id
        ];
    }

}
