<?php

namespace App\Listeners\Backend\SalesFlow\Commissions;

use App\Models\Commission\CommissionLedgers;
use App\Models\Office\Office;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUpload;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class AppointmentCommissionListener implements ShouldQueue
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
return 0;

        $userHasLead = UserHasLead::where('lead_id', $event->appointment->lead_id)
            ->where('position_id', 1)
            ->with('user', 'user.office')
            ->first();

        $sp1 = UserHasLead::where('lead_id', $event->appointment->lead_id)
            ->where('position_id', 2)
            ->where('user_id', $userHasLead->user_id)
            ->count();

        if ($sp1 > 0){
            dump('is sp1');
            return;
        }

        $office = Office::where('id', $userHasLead->user->office_id)->first();
        if (!$userHasLead->user->pay_rate_id == 1 && !$office->commission_plan_id == 1) {
//            dump('No COmmission plan');

            return;
        }
        $pastCommission = CommissionLedgers::where('lead_id', $event->appointment->lead_id)
            ->where('type_id', 8)
            ->where('user_id', $userHasLead->user_id)
            ->count();

        if ($pastCommission > 0) {
            dump('past Commission');
            return;
        }
        $lead = Lead::where('id', $event->appointment->lead_id)->with(
            ['leadUploads' => function ($q) {
                $q->where('type', 'bill');
            }]
        )->first();

        if ($lead->leadUploads->count() || $lead->isCreditPass(true)) {
            $officeCommissionRegular = OfficeCommissions::where('office_id', $lead->origin_office_id)
                ->where('type_id', 8)
                ->first();
            if ($officeCommissionRegular)

            $commission = new CommissionLedgers();
            $commission->type_id = 8;
            $commission->lead_id = $lead->id;
            $commission->amount = $officeCommissionRegular->amount;
            $commission->office_id = $lead->origin_office_id;
            $commission->user_id = $userHasLead->user_id;
            $commission->save();

        }else{
            dump('perams not filled');
        }

    }
}
