<?php


namespace App\Http\Resources\Lead;


use App\Models\SalesFlow\Appointment\Appointment;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesPacketResource extends JsonResource
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
            'credit_doc_signed' => $this->credit_doc_signed,
            'nem_doc_signed' => $this->nem_doc_signed,
            'cpuc_doc_signed' => $this->cpuc_doc_signed,
            'submitted_for_permitting_date' => $this->submitted_for_permitting_date,
            'permitting_received_date' => $this->permitting_received_date,
            'ach_doc_signed' => !empty($this->ach_doc_signed),
            'solar_agreement_signed' => $this->solar_agreement_signed,
            'proposal_doc_signed' => $this->proposal_doc_signed,
            'submitted' => !empty($this->submitted),
            'sat' => !empty($this->sat),
            'install_date' => Appointment::where('lead_id', '=', $this->lead->id)
                ->where('type_id', '=', 5)
                ->first(),
            'site_survey_note' => $this->site_survey_note,
            'site_survey_date' => Appointment::where('lead_id', '=', $this->lead->id)
                ->where('type_id', '=', 4)
                ->first(),
            'design_plan_sent_date' => $this->design_plan_sent_date,
            'site_plan' => $this->site_plan,
            'pto' => !empty($this->pto),
            'converted' => $this->converted
        ];
    }
}
