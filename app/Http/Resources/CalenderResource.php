<?php


namespace App\Http\Resources;


use App\Models\Office\Office;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->type_id === 3 || $this->type_id === 6) {
            $color = 'blue';
        } else if ($this->type_id === 4 || $this->type_id === 5) {
            $color = 'green';
        } else {
            $color = 'purple';
        }

        if ($this->remote) {
            $color = 'black';
        }
        $frontStatus = null;
        if ($this->type_id === 6) {
            if ($this->lead->credit_status_id === 9) {
                $frontStatus = '🟡 ';
            } elseif ($this->lead->credit_status_id === 7) {
                $frontStatus = '🔴 ';
            }

            if (count($this->lead->leadUploads)) {
                $frontStatus .= '🧾 ';
            }
        }


        return [
            'id' => $this->id,
            'start' => $this->start_time,
            'end' => $this->finish_time,
            'user' => new UserResource($this->user),
//            'office' => $this->user->office,
            'backgroundColor' => $color,
            'title' => $frontStatus . '' . $this->subject,
            'comment' => $this->comment,
            'url' => '/dashboard/calendar/' . $this->id,
        ];
//        return parent::toArray($request);
    }
}
