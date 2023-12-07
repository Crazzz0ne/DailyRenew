<?php


namespace App\Http\Resources\Lead;


use App\Http\Resources\UserResource;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Line;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {
        $filledTime  = Carbon::parse($this->filled_time, 'America/Los_Angeles');
        $rtime =  Carbon::parse($this->created_at, 'America/Los_Angeles');


        $difference = $rtime->diffInMinutes($filledTime);
        return [
            'id' => $this->id,
            'requestingRep' => new UserResource($this->requestingUser),
            'filledRep' => new UserResource($this->filledUser),
            'position' => $this->position,

            'lead' => [
                'id' => $this->lead_id,
                'office_id' => $this->lead->office_id ?? null,
                'office_name' => $this->lead->office->name ?? null,
                'source' => $this->lead->source ?? null,
                'customer' => [
                    'full_name' => $this->lead->customer->full_name ?? null,
                    'language' => $this->lead->customer->language ?? null,
                    'state' => $this->lead->customer->state ?? null,
                ],
            ],
            'times' => [
                'filledTime' => $this->filled_time,
                'difference' => $difference,
                'rTime' => $this->created_at,
            ],
            'type' => $this->type,
            'urgent' =>$this->urgent ? true : false,
            'created_at' => $this->created_at,
            'related' => LineResource::collection($this->whenLoaded('related')),
        ];

    }


}
