<?php

namespace App\Listeners\Backend\SalesFlow\Commissions;

use App\Models\Commission\CommissionLedgers;
use App\Models\Office\Office;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class CloseCommissionListener implements ShouldQueue
{
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
        $lead = Lead::where('id', $event->leadId)->first();
        $now = Carbon::parse($lead->created_at);
        if (Carbon::now()->greaterThanOrEqualTo($now)) {
            return;
        }
        $userHasLead = UserHasLead::where('lead_id', $lead->id)
            ->where('position_id', 3)
            ->with('user', 'user.office')
            ->first();


        $office = Office::where('id', $userHasLead->user->office_id)->first();
        if ($userHasLead->user->pay_rate_id !== 1 || $office->commission_plan_id !== 1) {
            return;
        }
        $typeId = 11;

        $pastCommission = CommissionLedgers::where('lead_id', $lead->id)
            ->where('type_id', $typeId)
            ->where('user_id', $userHasLead->user_id)
            ->count();
        if ($pastCommission) {
            return;
        }
        $lead = Lead::where('id', $lead->id)->with(
            ['leadUploads' => function ($q) {
                $q->where('type', 'bill');
            }]
        )->first();

        if ($lead->leadUploads->count() || $lead->isCreditPass(true)) {
            $officeCommissionRegular = OfficeCommissions::where('office_id', $lead->origin_office_id)
                ->where('type_id', $typeId)
                ->first();

            $commission = new CommissionLedgers();
            $commission->type_id = $typeId;
            $commission->lead_id = $lead->id;
            $commission->amount = $officeCommissionRegular->amount;
            $commission->office_id = $lead->origin_office_id;
            $commission->user_id = $userHasLead->user_id;
            $commission->save();

        }

    }
}
