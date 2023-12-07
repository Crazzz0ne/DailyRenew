<?php


namespace App\Http\Resources\Epc;


use App\Http\Resources\Market\PowerCompany\PowerCompanyResource;
use App\Models\Epc\EpcFinance;
use App\Models\Epc\PowerCompany\PowerCompany;
use App\Models\Epc\SolarModule;
use Illuminate\Http\Resources\Json\JsonResource;

class EpcResource extends JsonResource
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
            'adders' => EpcAddersResource::collection($this->whenLoaded('adders')),
//            'systems' => EpcAddersResource::collection($this->whenLoaded('equipment')),
            'modules' => SolarModulesResource::collection($this->whenLoaded('solarModules')),
            'inverters' => SolarModulesResource::collection($this->whenLoaded('solarInverters')),
            'creditStatus' => CreditStatusResource::collection($this->whenLoaded('creditStatus')),
            'powerCompany' => PowerCompanyResource::collection(PowerCompany::where('id', '>', 0)->where('epc_id', 1)->get()->sortBy('name')),
            'financeOptions' => EpcFinanceResource::collection($this->whenLoaded('finance')),
        ];
    }

}
