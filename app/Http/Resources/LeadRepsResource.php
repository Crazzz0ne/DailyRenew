<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadRepsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
//                return parent::toArray($request);
        return [
            'id' => $this->id,
            'position_name' => $this->position->name ?? '',
            'position_id' => $this->position_id ?? '',
            'user_id' => $this->user_id ?? '',
            'first_name' => $this->user->first_name ?? '',
            'last_name' => $this->user->last_name ?? '',
            'phone_number' => $this->user->phone_number ?? '',
            'email' => $this->user->email ?? '',
            'login' => $this->users->partnerLink ?? '',
            'created_at' => $this->created_at ?? '',
        ];
    }
}
