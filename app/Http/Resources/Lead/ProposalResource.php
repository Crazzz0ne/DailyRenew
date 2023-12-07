<?php


namespace App\Http\Resources\Lead;


use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
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
            'his_license' => $this->his_license,
            'system_size' => $this->system_size,
            'offset' => $this->offset,
            'solar_rate' => $this->solar_rate,
            'monthly_payment' => $this->monthly_payment,
            'ppw' => $this->ppw,
            'needed_by' => $this->epc,
            'adders' => $this->adders,
            'system' => $this->system,
            'credit_score' => $this->credit_score
        ];
    }
}
