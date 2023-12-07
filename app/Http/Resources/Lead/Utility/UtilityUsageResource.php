<?php


namespace App\Http\Resources\Lead\Utility;


use Illuminate\Http\Resources\Json\JsonResource;

class UtilityUsageResource extends JsonResource
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
            'utility_id' => $this->utility_id,
            'jan' => [
                'kwh' => $this->jan_kwh,
                'bill' => $this->jan_bill
            ],
            'feb' => [
                'kwh' => $this->feb_kwh,
                'bill' => $this->feb_bill
            ],
            'mar' => [
                'kwh' => $this->mar_kwh,
                'bill' => $this->mar_bill
            ],
            'apr' => [
                'kwh' => $this->apr_kwh,
                'bill' => $this->apr_bill
            ],
            'may' => [
                'kwh' => $this->may_kwh,
                'bill' => $this->may_bill
            ],
            'jun' => [
                'kwh' => $this->jun_kwh,
                'bill' => $this->jun_bill
            ],
            'jul' => [
                'kwh' => $this->jul_kwh,
                'bill' => $this->jul_bill
            ],
            'aug' => [
                'kwh' => $this->aug_kwh,
                'bill' => $this->aug_bill
            ],
            'sep' => [
                'kwh' => $this->sep_kwh,
                'bill' => $this->sep_bill
            ],
            'oct' => [
                'kwh' => $this->oct_kwh,
                'bill' => $this->oct_bill
            ],
            'nov' => [
                'kwh' => $this->nov_kwh,
                'bill' => $this->nov_bill
            ],
            'dec' => [
                'kwh' => $this->dec_kwh,
                'bill' => $this->dec_bill
            ]
        ];
    }

}
