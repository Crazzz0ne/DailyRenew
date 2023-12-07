<?php


namespace App\Http\Resources\Epc;


use Illuminate\Http\Resources\Json\JsonResource;

class CreditStatusResource   extends JsonResource
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
            'label' => $this->name,
        ];
    }

}
