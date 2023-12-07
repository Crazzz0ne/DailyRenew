<?php


namespace App\Http\Resources\Commission;


use App\Http\Resources\LeadsResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'subject' => $this->subject,
            'type' => $this->type->name,
            'type_id' => $this->type_id,
            'customer' => $this->lead->customer->full_name ?? '',
            'lead_id' => $this->lead_id,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'approved' => $this->approved ? true : false,
            'office' => $this->office
        ];
    }

}
