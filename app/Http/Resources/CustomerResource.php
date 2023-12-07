<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
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
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'spouse_name' => $this->spouse_name,
			'street_address' => $this->street_address,
			'city' => $this->city,
			'state' => $this->state,
			'zip_code' => $this->zip_code,
			'home_phone' => $this->home_phone,
			'cell_phone' => $this->cell_phone,
			'email' => $this->email,
			'language' => $this->language,
            'dob' => $this->dob,
            'last_four' => $this->last_four,
            'sales_force_url' => $this->sales_force_url,
            'twilio_number' => $this->twilio_number
		];
//        return parent::toArray($request);
	}
}
