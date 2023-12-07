<?php


namespace App\Http\Resources\Market\PowerCompany;

use App\Models\Office\Market\PowerCompany\Program;
use Illuminate\Http\Resources\Json\JsonResource;


class PowerCompanyResource extends JsonResource
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
            'plan' => ProgramResource::collection(Program::where('power_company_id', '=', $this->id)
                ->where('type', '=', 'rate_plan')->get()),
            'discount' => ProgramResource::collection(Program::where('power_company_id', '=', $this->id)
                ->where('type', '=', 'discount_program')->get()),
        ];
    }
}
