<?php


namespace App\Http\Resources\Lead;


use Illuminate\Http\Resources\Json\JsonResource;

class SystemResource extends JsonResource
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
            'system_size' => $this->system_size,
            'offset' => $this->offset,
            'yearly_production' => $this->yearly_production,
            'epc_finance_id' => $this->epc_finance_id,
            'modules_id' => $this->modules_id,
            'modules_count' => $this->modules_count,
            'inverter_id' => $this->inverter_id,
            'solar_rate' => $this->solar_rate,
            'monthly_payment' => $this->monthly_payment,
            'contract_amount' => $this->contract_amount,
            'roof_work' => $this->roof_work,
            'adders' => $this->adders,
            'system' => $this->system,
            'external_proposal_id' => $this->external_proposal_id,
            'updated_at' => $this->updated_at,
            'complete_url' => $this->complete_url
        ];
    }

}
