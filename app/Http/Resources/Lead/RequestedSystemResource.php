<?php


namespace App\Http\Resources\Lead;


use Illuminate\Http\Resources\Json\JsonResource;

class RequestedSystemResource  extends JsonResource
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
            'proposed_system_id' => $this->requested_system_id,
            'adders' => $this->adders,
            'sales_rep_note' => $this->sales_rep_note,
            'pb_note' => $this->pb_note,
            'modules_id' => $this->modules_id,
            'modules_count' => $this->modules_count,
            'inverter_id' => $this->inverter_id,
            'epc_system_equipment_id' => $this->epc_system_equipment_id,
            'epc_finance_id' => $this->epc_finance_id,
            'epc_system_id' => $this->epc_system_id,
            'lead_id' => $this->lead_id,
            'monthly_payment' => $this->monthly_payment,
            'offset' => $this->offset,
            'solar_rate' => (float)$this->solar_rate,
            'system_size' => $this->system_size,
            'ppw' => $this->ppw,
            'roof_work' => $this->roof_work,
            'approved' => $this->approved,
            'updated_at' => $this->updated_at
        ];
    }
}
