<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


class LeadUploadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $disk = Storage::disk('s3');
        $url = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), $this->path, Carbon::now()->addMinutes(600), []);

        return [
            'id' => $this->id,
            'lead_id' => $this->lead_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'size' => $this->size,
            'path' => $url,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at
        ];
    }
}
