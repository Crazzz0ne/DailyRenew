<?php


namespace App\Http\Resources\Lead;


use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadStatusResource extends JsonResource
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
            'name' => $this->name
        ];
    }




}
