<?php

namespace App\Http\Resources;

use App\Http\Resources\Roles\RolesResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param Request $request
	 * @return array
	 */
	public function toArray($request)
	{

//         dd($this->);
//        dd($this->isExecutive());
//        TODO:paginate for
		$data = [
			'id' => $this->id,
			'name' => ["first" => $this->first_name, "last" => $this->last_name],
            'fullName' => $this->first_name. ' '. $this->last_name,
            'phone' => $this->phone_number,
            'email' => $this->email,
			'office' => new OfficeResource($this->whenLoaded('office')),
            'office_id' => $this->office_id,
			'positions' => PositionResource::collection($this->whenLoaded('positions')),
            'gravatar' => $this->picture,
            'roles' => RolesResource::collection($this->whenLoaded('roles')),
            'terminated' => $this->terminated  ? true : false,
            'ok' => true,
            'languages' => $this->languages,
            'remote_option' => $this->remote_option
		];
		return $data;


	}
}
