<?php

namespace App\Http\Resources\Customer;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'body' => $this->body,
            'status' => $this->status,
            'images' => CustomerMessageImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at,

        ];
    }
}
