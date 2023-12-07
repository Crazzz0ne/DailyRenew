<?php

namespace App\Listeners\Backend\SalesFlow\Commissions;

use App\Models\Commission\CommissionLedgers;
use App\Models\Office\Office;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveOpenersPayListener implements ShouldQueue
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {

return;
        if ($event->lead->isCreditPass(true)) {
            return;
        }
        $userHasLead = UserHasLead::where('lead_id', $event->lead->id)
            ->where('position_id', 1)
            ->with('user', 'user.office')
            ->first();
//        ToDO:This is weird had to pull the office again because it would not let me access it off the user has lead.
        $office = Office::where('id', $userHasLead->user->office_id)->first();

        if ($userHasLead->user->pay_rate_id !== 1 || $office->commission_plan_id !== 1) {
            return;
        }


        $leadWithBill = $event->lead->leadUploads->filter(function ($value, $key) {
            if ($value->type === 'bill') {
                return true;
            } else {
                return false;
            }
        });

        if ($leadWithBill->count() < 1) {

            $pastCommission = CommissionLedgers::where('lead_id', $event->lead->id)
                ->where('type_id', 8)
                ->where('user_id', $userHasLead->user_id)
                ->get();
            if ($pastCommission->count() == 1) {
                $commission = new CommissionLedgers();
                $commission->type_id = 8;
                $commission->lead_id = $event->lead->id;
                $commission->amount = -$pastCommission->first()->amount;
                $commission->office_id = $event->lead->origin_office_id;
                $commission->user_id = $userHasLead->user->id;
                $commission->save();
            }
        }


    }
}
