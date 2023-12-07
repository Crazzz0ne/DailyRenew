<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\OfficeResource;
use App\Http\Resources\Roles\PermissionsResource;
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
            'office' => new OfficeResource($this->whenLoaded('homeOffice')),
            'office_id' => $this->office_id,
            'gravatar' => $this->picture,
            'roles' => RolesResource::collection($this->roles),
            'permissions' => PermissionsResource::collection($this->permissions),
            'terminated' => $this->terminated,
            'languages' => $this->languages,
            'payscale' => $this->payscale,
            'timezone' => $this->timezone,
            'team_id' => $this->team_id,
            'remote_option' => $this->remote_option,
            'created_at' => $this->created_at
		];
		return $data;


	}
}
