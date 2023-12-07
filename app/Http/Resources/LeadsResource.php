<?php

namespace App\Http\Resources;

use App\Http\Resources\Lead\LeadDispositionResource;
use App\Http\Resources\Lead\LeadUtilityResource;
use App\Http\Resources\Lead\ProposalResource;
use App\Http\Resources\Lead\SalesPacketResource;
use App\Http\Resources\Lead\SystemResource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Disposition;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadUpload;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\RoofType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadsResource extends JsonResource
{
    /**
     * @var mixed
     */


    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'customer_id' => $this->customer_id,
            'closedAt' => $this->close_date,
            'epc_id' => $this->epc_id,
            'epcName' => $this->epcName,
            'status' => $this->statusName->name ?? '',
            'stale' => $this->stale,
            'status_id' => $this->status_id,
            'source' => $this->source,
            'integrations_approved' => $this->integrations_approved,
            'jeopardy' => $this->jeopardyName->name ?? '',
            'jeopardy_id' => $this->jeopardy_id,
            'office_id' => $this->office_id,
            'origin_office_id' => $this->origin_office_id,
            'origin_office_name' => $this->originOffice->name ?? null,
            'office_name' => $this->office->name,
            'office' => new OfficeResource($this->whenLoaded('office')),
            'requiredIntegrations' => $this->office->require_integrations,
            'credit_status_id' => $this->credit_status_id,
            'market_name' => $this->office->market->name,
            'utility_id' => $this->utility_id,
            'proposed_system_id' => $this->proposed_system_id,
            'system_id' => $this->system_id,
            'system' =>  new SystemResource($this->whenLoaded('system')),
            'sales_packet_id' => $this->sales_packet_id,
//            'login' => new LoginResource($this->whenLoaded('logins')),
            'reps' => RepresentativeResource::collection($this->whenLoaded('reps')),
            'roof' => new LeadRoofResource($this->whenLoaded('leadRoof')),
            'appointments' => LeadAppointmentResource::collection($this->whenLoaded('appointments')),
            'uploads' => LeadUploadResource::collection($this->whenLoaded('leadUploads')),
            'salesPacket' => new SalesPacketResource($this->whenLoaded('salesPacket')),
            'siteSurveyQuestions' => $this->siteSurveyQuestions,
            'epc_owner_id' => $this->epc_owner_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

