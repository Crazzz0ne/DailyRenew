<?php

namespace App\Listeners\Backend\SalesFlow;

use App\Models\BatchedJob;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateZapierListener implements ShouldQueue
{

    public function __construct()
    {
        //
    }


    public function handle($event)
    {


        $newData = $this->collectNewData($event);

        $batchedJob = BatchedJob::where('lead_id', $event->lead->id)
            ->where('type', 'zapier')
            ->where('deleted_at', null)
            ->first();
        if ($batchedJob) {

            $oldData = json_decode($batchedJob->data, true);
            $mergedData = $this->mergeData($oldData, $newData);
            $batchedJob->data = json_encode($mergedData);
            $batchedJob->save();
        } else {
            BatchedJob::create([
                'type' => 'zapier',
                'data' => json_encode($newData),
                'lead_id' => $event->lead->id,
            ]);
        }
    }

    private function mergeData($oldData, $newData)
    {
// Merge old and new data arrays
        $mergedData = array_merge($oldData, $newData);
        return $mergedData;
    }

    private function collectNewData($event)
    {
        $data = [];

        if ($event->type === 'credit') {
            $data = array_merge($data, $this->checkCreditPass($event->lead));
        }

        if ($event->type === 'bill') {
            $utilityBill = $this->checkUtilityBill($event->lead);
            if (!$utilityBill) {
                return false;
            }
            $data = array_merge($data, $utilityBill);
        }
        if ($event->type === 'sit') {
            $data = array_merge($data, $this->checkSit($event->lead));
        }

        if ($event->type === 'appointment') {
            $data = array_merge($data,  $this->checkAppointment($event->lead));
        }

        if ($event->type === 'installed') {
            $data = array_merge($data, $this->setInstalled($event->lead));
        }

        $baseData = [
            'created_at' => Carbon::parse($event->lead->created_at)->timezone('America/Los_Angeles')->toDateTimeString(),
            'lead_id' => $event->lead->id,
            'credit_status' => $event->lead->creditStatus->name,
            'time_stamp' => Carbon::now()->timezone('America/Los_Angeles')->toDateTimeString(),
            'customer_name' => $event->lead->customer->full_name,
            'customer_address' => $event->lead->customer->street_address.', '. $event->lead->customer->city.', '. $event->lead->customer->state.','. $event->lead->customer->zip_code,
            'office_name' => $event->lead->office->name . ', ' . $event->lead->originOffice->name,
            'stage' => $event->lead->statusName->name,
            'close_date' => $event->lead->close_date,
        ];

        $data = array_merge(
            $data,
            $this->checkOpener($event->lead),
            $this->checkSp1($event->lead),
            $this->checkSp2($event->lead),
            $baseData
        );

        return $data;

    }

    function checkOpener($lead)
    {
        $user = UserHasLead::where('lead_id', $lead->id)->where('deleted_at', null)->where('position_id', 1)->first();
        if ($user) {
            return [
                'opener' => $user->user->full_name ?? 'null',
            ];
        }
        return ['opener' => 'null'];
    }

    function checksp1($lead)
    {
        $user = UserHasLead::where('lead_id', $lead->id)->where('deleted_at', null)->where('position_id', 2)->first();
        if ($user) {
            return [
                'sp1' => $user->user->full_name ?? 'null',
            ];
        }
        return ['sp1' => 'null'];
    }

    function checksp2($lead)
    {
        $user = UserHasLead::where('lead_id', $lead->id)->where('deleted_at', null)->where('position_id', 3)->first();
        if ($user) {
            return [
                'sp2' => $user->user->full_name ?? 'null',
            ];
        }
        return ['sp2' => 'null'];
    }

    function checkAppointment($lead)
    {
        $appointment = $lead->appointments()->where('deleted_at', null)->where('type_id', 6)->first();
        if ($appointment) {
            return [
                'date_appointment_set' => Carbon::parse($appointment->created_at)->timezone('America/Los_Angeles')->toDateTimeString(),
                'date_of_appointment' => Carbon::parse($appointment->start_time)->timezone('America/Los_Angeles')->toDateTimeString()
            ];
        }
        return [
            'date_appointment_set' => 0,
            'date_of_appointment' => 0
        ];
    }

    function checkSit($lead)
    {
        if ($lead->salesPacket->sat) {
            return ['sit' => Carbon::now()->timezone('America/Los_Angeles')->toDateTimeString()];
        } else {
            return ['sit' => 0];
        }
    }

    function setInstalled($lead)
    {
        return ['installed' => Carbon::now()->timezone('America/Los_Angeles')->toDateTimeString()];
    }

    function checkUtilityBill($lead)
    {
        $utilityBill = $lead->leadUploads->where('type', 'bill')->first();
        if ($utilityBill) {
            return ['utility_bill' => Carbon::parse($utilityBill->created_at)->timezone('America/Los_Angeles')->toDateTimeString()];
        } else {
            return ['utility_bill' => 0];
        }

    }

    function checkCreditPass($lead)
    {
        if ($lead->queues->count()) {
            $now = Carbon::now()->timezone('America/Los_Angeles')->toDateTimeString();
            if ($lead->queues->where('type', 'credit_app')->count() > 'null') {
                $queue = $lead->queues->where('type', 'credit_app')->first();
                $sp1 = UserHasLead::where('lead_id', $lead->id)->where('position_id', 2)->first();
                if (!$sp1) {
                    return [
                        'credit_pass' => $now,
                    ];
                }
                if ($sp1->user_id === $queue->requested_user_id) {
                    return [
                        'credit_requested_by' => $sp1->user->name,
                        'credit_pass' => $now,
                    ];
                } else {
                    return [
                        'credit_requested_by' => $queue->requestingUser->name,
                        'credit_pass' => $now,
                    ];
                }

            }
            return [
                'credit_pass' => $now,
            ];
        }
        if ($lead->isCreditPass()) {
            return ['credit_pass' => Carbon::now()->timezone('America/Los_Angeles')->toDateTimeString()];
        } else {
            return ['credit_pass' => 0];
        }
    }


}

