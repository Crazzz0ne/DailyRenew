<?php

namespace App\Http\Resources\Office;

use App\Http\Resources\OfficeResource;
use App\Http\Resources\PositionResource;
use App\Http\Resources\Roles\RolesResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource  extends JsonResource
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
            'name' => $this->name,
            'teamMembers' => UserResource::collection($this->users),
            'teamCaptain' => $this->captain_id,


        ];
        return $data;


    }
}
