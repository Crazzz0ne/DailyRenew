<?php


namespace App\Repositories\Backend\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\TextEvent;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use League\Flysystem\Exception;

class AppointmentRepository extends BaseRepository
{
    public function model()
    {
        return Appointment::class;

    }

    /**
     * @param array $data
     *
     * @return Appointment
     * @throws Throwable
     * @throws Exception
     */

    public function unAssignedAppointment(): int
    {
        if (auth()->user()->hasAnyRole('manager')) {
            return $appointment = Appointment::where('user_id', '=', null)
                ->whereHas('user', function ($q) {
                    $q->where('office_id', auth()->user()->office_id);
                })
                ->count();
        } elseif (auth()->user()->hasAnyRole('executive', 'administrator')) {
            return $appointment = Appointment::where('user_id', '=', null)
                ->count();
        }
    }

    public function unAssignedAppointmentList()
    {
//        return Appointment::where('user_id', '=', null)->get()->pluck('lead_id', 'id');

    }

    public function textNotifySP1ofSP2($lead, $sp2ID, $startTime)
    {
        $reps = UserHasLead::where('lead_id', '=', $lead->id)
            ->where('position_id', '=', 2)
            ->get();

        foreach ($reps as $rep) {
            $SP2 = User::where('id', $sp2ID)->get()->first();
            $SP1phone = User::where('id', $rep->user_id)->get()->first();
            $customerName = $lead->customer->full_name;
            $body = 'SP2 ' . $SP2->fullname . ' will see ' . $customerName . ' on ' . $startTime;
            try {


                TwilioSMSController::sendSms($SP1phone->phone_number, $body);


            } catch (Exception $e) {

                \Log::info('Tried to send a text to sp1 about sp2 being added ' . ' ' . $e);
            }

        }
    }

    public function textNotifySP2OfAssigned($lead, $SP2, $startTime)
    {
        $SP1 = UserHasLead::where('lead_id', '=', $lead->id)->where('position_id',  3)->first();


        if ($SP1) {
            $customerName = $lead->customer->full_name;
            $body = 'You have been assigned to an appointment for ' . $customerName . ' @ ' . Carbon::parse($startTime)->timezone($SP1->user->timezone)->toDayDateTimeString() . ' please show up 10 minutes early ' .
                URL::to('/') . '/dashboard/lead/' . $lead->id;

            if (app()->environment('production')) {
                TwilioSMSController::sendSms($SP1->user->phone_number, $body);
            }
        }

    }


}
