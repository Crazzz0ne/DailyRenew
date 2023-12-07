<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Storage;

class CustomerMessageImageResource extends JsonResource
{
    public function toArray($request)
    {
        $disk = Storage::disk('s3');
        $url = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), $this->path, Carbon::now()->addMinutes(600), []);

        return [
            'id' => $this->id,
            'path' => $url,
            'created_at' => $this->created_at,
        ];
    }
}
