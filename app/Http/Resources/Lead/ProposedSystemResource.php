<?php


namespace App\Http\Resources\Lead;


use App\Http\Resources\LeadUploadResource;
use App\Models\SalesFlow\Appointment\Appointment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProposedSystemResource extends JsonResource
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
            'lead_id' => $this->lead_id,
            'external_proposal_id' => $this->external_proposal_id,
            'monthly_payment' => $this->monthly_payment,
            'solar_rate' => $this->solar_rate,
            'roof_work' => $this->roof_work,
            'yearly_production' => $this->yearly_production,
            'offset' => $this->offset,
            'adders' => $this->adders,
            'modules_id' => $this->modules_id,
            'modules_count' => $this->modules_count,
            'inverter_id' => $this->inverter_id,
            'epc_system_id' => $this->epc_system_id,
            'epc_finance_id' => $this->epc_finance_id,
            'proposalDoc' => new LeadUploadResource($this->proposalDoc),
            'sitePlanDoc' => new LeadUploadResource($this->sitePlanDoc),
            'path' => $this->path,
            'contract_amount' => $this->contract_amount,
            'testDoc' => new LeadUploadResource($this->testDoc),
            'savingsBreakDown' => new LeadUploadResource($this->savingsBreakDown),
            'rep_design_approved' => $this->rep_design_approved,
            'pb_design_approved' => $this->pb_design_approved,

            'updated_at' => $this->updated_at
        ];
    }

}
