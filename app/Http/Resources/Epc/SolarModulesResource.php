<?php


namespace App\Http\Resources\Epc;


use Illuminate\Http\Resources\Json\JsonResource;

class SolarModulesResource extends JsonResource
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
            'active' => $this->active,
            'watts' => $this->watts,
            'model' =>  $this->model,
            'epc_id' => $this->epc_id
        ];
    }
}
