<?php

namespace App\Http\Controllers\Api\SalesFlow\Lead;

use App\Http\Resources\LeadAppointmentResource;
use App\Http\Resources\LeadUploadResource;
use App\Http\Resources\OfficeResource;
use App\Http\Resources\RepresentativeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadDashBoardResource extends JsonResource
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
            'customer' => [
                'full_name'=> $this->customer->first_name.' '. $this->customer->last_name,
                'city'=> $this->customer->city,
                'phone'=> $this->customer->cell_phone ?? $this->customer->home_phone,
                'email'=> $this->customer->email,
            ],
            'customer_id' => $this->customer_id,
            'status' => $this->statusName->name ?? '',
            'status_id' => $this->status_id,
            'source' => $this->source,
            'office_id' => $this->office_id,
            'origin_office_id' => $this->origin_office_id,
            'origin_office_name' => $this->originOffice->name ?? null,
            'office_name' => $this->office->name,
            'office' => new OfficeResource($this->whenLoaded('office')),
            'credit_status_id' => $this->credit_status_id,
//            'login' => new LoginResource($this->whenLoaded('logins')),
            'reps' => RepresentativeResource::collection($this->whenLoaded('reps')),
            'appointments' => LeadAppointmentResource::collection($this->whenLoaded('appointments')),
            'uploads' => LeadUploadResource::collection($this->whenLoaded('leadUploads')),
            'epc_owner_id' => $this->epc_owner_id,
            'created_at' => $this->created_at,
        ];
    }
}
