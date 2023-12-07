<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class NotificationResource  extends JsonResource
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
            'notification_id' => $this->id,
            'read_at' => $this->read_at,
            'url' => $this->url,
            'img' => $this->img,
            'body' => json_decode($this->data)->body,
            'time' => $this->time,
        ];
    }
}
