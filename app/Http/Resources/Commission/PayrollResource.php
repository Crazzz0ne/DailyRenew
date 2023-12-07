<?php


namespace App\Http\Resources\Commission;


use App\Http\Resources\UserResource;
use App\Models\Commission\CommissionLedgers;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollResource  extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request)
    {

       $ledger = CommissionLedgers::whereIn('id', $this->commissions)->get();
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('User')),
            'repName' => $this->user->fullName,
            'amount' => $this->amount,
            'commissions' => $this->commissions,
            'commissionDetails' => CommissionResource::collection($ledger),
            'created' => $this->created_at
        ];
    }

}
