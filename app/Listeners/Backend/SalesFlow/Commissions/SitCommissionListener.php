<?php

namespace App\Listeners\Backend\SalesFlow\Commissions;

use App\Models\Commission\CommissionLedgers;
use App\Models\Office\Office;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SitCommissionListener implements ShouldQueue
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


//        $now = Carbon::parse($event->lead->created_at);
//        if (Carbon::now()->greaterThanOrEqualTo($now)) {
//            return;
//        }
        //        TODO: Should probably solidify this in the DB

        $selfGen = 11;
        $sitType = 3;
        $billType = 9;
        $creditType = 10;

        $sp1 = UserHasLead::where('lead_id', $event->lead->id)
            ->where('position_id', 2)
            ->with('user', 'user.office')
            ->first();

        if (!$sp1){
            return;
        }
        $userId = $sp1->user_id;

        $payRate = $sp1->user->pay_rate_id;
        $opener = UserHasLead::where('lead_id', $event->lead->id)
            ->where('user_id', $userId)
            ->where('position_id', 1)
            ->with('user', 'user.office')
            ->count();
        //        ToDO:This is weird had to pull the office again because it would not let me access it off the user has lead.
        $office = Office::where('id', $sp1->user->office_id)->first();
//        If they don't have a commission plan Then this is not for them.
        if (!$sp1->user->pay_rate_id || !$office->commission_plan_id) {

            return;
        }
        if ($this->isSp2($event->lead, $sp1->user_id)) {
            return;
        }

//      Set Sit Pay

        $this->processCommission($event->lead, $userId, $sitType, $payRate);
//      Set Self Gen Pay
        if ($opener == 1) {
            $this->processCommission($event->lead, $userId, $selfGen, $payRate);
        }

        $lead = Lead::where('id', $event->lead->id)->with(
            ['leadUploads' => function ($q) {
                $q->where('type', 'bill');
            }]
        )->first();

//      Credit pass Pay
        if ($lead->iscreditPass(true)) {
            if ($this->checkCreditPass($lead, $userId)) {
                $this->processCommission($event->lead, $userId, $creditType, $payRate);
            }
        }
//        Upload Pay
        if ($lead->leadUploads->count()) {
            $uploadedTheLead = $lead->leadUploads->filter(function ($value) use ($userId) {
                if (($value->user_id === 1 || $value === $userId) && $value->type === 'bill') {
                    return true;
                } else {
                    return false;
                }
            });

            if (count($uploadedTheLead) == 0) {
                return;
            }
            $this->processCommission($event->lead, $userId, $billType, $payRate);
        }
    }

    function processCommission($lead, $userId, $commissionType, $payRate)
    {
        if ($this->checkPreviousCommission($lead, $userId, $commissionType)) {
            $commission = $this->getCommission($lead, $commissionType, $payRate);
            if (!$commission) {
                return;
            }
            $this->saveCommission($lead, $userId, $commission->amount, $commissionType);
        }
    }

    function isSp2($lead, $sp1)
    {
        return UserHasLead::where('lead_id', $lead->id)->where('position_id', 3)->where('user_id', $sp1)->count();
    }

    function getCommission($lead, $typeId, $rateId)
    {
//        dump($lead->origin_office_id, $rateId, $typeId);
        return OfficeCommissions::where('office_id', $lead->origin_office_id)
            ->where('pay_rate_id', $rateId)
            ->where('type_id', $typeId)
            ->first();
    }

    function saveCommission($lead, $userId, $amount, $type)
    {
        $commission = new CommissionLedgers();
        $commission->type_id = $type;
        $commission->lead_id = $lead->id;
        $commission->amount = $amount;
        $commission->office_id = $lead->origin_office_id;
        $commission->user_id = $userId;
        $commission->save();
    }

    function checkCreditPass($lead, $userId)
    {

        $checks = $lead->audits()->where('user_id', $userId)->latest()->get();
        foreach ($checks as $check) {
            if (isset($check['new_values']['credit_status_id'])) {
                return true;
            }
        }
    }

    function checkPreviousCommission($leadId, $typeId, $userId): bool
    {
        $pastCommission = CommissionLedgers::where('lead_id', $leadId)
            ->where('type_id', $typeId)
            ->where('user_id', $userId)
            ->count();
        if ($pastCommission == 0) {
            return true;
        } else {
            return false;
        }
    }
}
