<?php


namespace App\Http\Resources\Epc;


use Illuminate\Http\Resources\Json\JsonResource;

class EpcFinanceResource   extends JsonResource
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
            'rate' => $this->rate,
            'term' => $this->term,
            'fee' => $this->fee,
            'epc_id' => $this->epc_id,
            'finance_id' => $this->finance_id,
        ];
    }

}
