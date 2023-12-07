<?php

namespace App\Console\Commands;

use App\Models\Commission\CommissionLedgers;
use App\Models\Office\OfficeCommissions;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AppointmentPayrollCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:appointmentCom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sets appointment comms for people that get them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $appointments = Appointment::where('created_at', '>', Carbon::parse('2022-02-25 06:00:00')
            ->toDateTimeString())->where('type_id', 6)
            ->with(['lead', 'lead.reps', 'lead.leadUploads' => function ($q) {
                $q->where('type', 'bill');
            }])->get();
        $i = 0;

        foreach ($appointments as $appointment) {
            $opener = $appointment->lead->reps->filter(function ($rep, $key) {
                if ($rep->pivot->position_id == 1) {
                    return $rep;
                }
                return false;
            });
            $opener->all();
            $opener = $opener->first();
            $sp1 = $appointment->lead->reps->filter(function ($rep, $key) {
                if ($rep->pivot->position_id == 2) {
                    return $rep;
                }
                return false;
            });
            $sp1 = collect($sp1->all());
            $sp1 = $sp1->first();

            if ($opener) {
                $openerId = (int) $opener->id;
                if ($sp1) {
                    if ($sp1->id === $opener->id) {
                        continue;
                    }
                    if ($this->checkUploads($appointment->lead, $sp1->id)) {
                        $this->processCommission($appointment->lead, $openerId, 8, 1);
                        continue;
                    }
                }
                if ($appointment->lead->isCreditPass(true)) {
                    $this->processCommission($appointment->lead, $openerId, 8, 1);
                }
            }
        }

    }

    function checkUploads($lead, $userId)
    {
        if ($lead->leadUploads->count()) {
            return $lead->leadUploads->filter(function ($value) use ($userId) {
                if (($value->user_id === 1 || $value === $userId) && $value->type === 'bill') {
                    return true;
                } else {
                    return false;
                }
            });
        }
        return 0;
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
        $pastCommission = CommissionLedgers::where([['lead_id', '=', $leadId], ['user_id', $userId], ['type_id', '=', $typeId]])
            ->count();
        if ($pastCommission === 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function processCommission($lead, $userId, $commissionType, $payRate)
    {
        if ($this->checkPreviousCommission($lead->id,  $commissionType, $userId)) {
            $commission = $this->getCommission($lead, $commissionType, $payRate);
            if (!$commission) {
                return;
            }
            $this->saveCommission($lead, $userId, $commission->amount, $commissionType);
        }
    }
}

