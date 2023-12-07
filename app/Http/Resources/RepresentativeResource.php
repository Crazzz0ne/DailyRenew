<?php

namespace App\Http\Resources;

use App\Models\SalesFlow\Position\Position;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepresentativeResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param Request $request
	 * @return array
	 */
	public function toArray($request)
	{
//         dd($this);

		return [

            'id' => $this->id,
            'user_id' => $this->pivot->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'position_id' => $this->pivot->position_id,
            'position_name' => Position::where('id', '=', $this->pivot->position_id)->first()->name,
            'phone_number' => $this->phone_number,
        ];
	}
}
