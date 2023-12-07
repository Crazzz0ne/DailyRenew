<?php


namespace App\Http\Resources\Lead\Utility;


use Illuminate\Http\Resources\Json\JsonResource;

class LeadUtilityResource extends JsonResource
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
            'kw_year_usage' => $this->kw_year_usage,
            'power_company_id' => $this->power_company_id,
            'power_program_id' => $this->power_program_id,
            'power_discount_id' => $this->power_discount_id,
            'average_bill' => $this->average_bill,
            'name_on_bill' => $this->name_on_bill,
        ];
    }

}
