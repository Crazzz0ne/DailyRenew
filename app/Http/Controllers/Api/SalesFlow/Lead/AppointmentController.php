<?php

namespace App\Http\Controllers\Api\SalesFlow\Lead;

use App\Events\Backend\SalesFlow\AppointmentBookedEvent;
use App\Events\Backend\SalesFlow\Customer\CustomerAppointmentEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\ProposedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\RequestedSystemEvent;
use App\Events\Backend\SalesFlow\LeadNewAppointment;
use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Events\Backend\SalesFlow\RepAddedToLeadEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Helpers\Appointments\LocationHelper;
use App\Helpers\Appointments\AppointmentHelper;
use App\Helpers\Appointments\NewAppointmentHelper;
use App\Helpers\Auth\GoogleOAuth2;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\LeadAppointmentResource;
use App\Http\Resources\LeadRepsResource;
use App\Http\Resources\LeadsResource;
use App\Http\Resources\UserResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Appointment\Availability;
use App\Models\SalesFlow\Appointment\Type;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\Backend\SalesFlow\Lead\AppointmentRepository;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Slack;
use function PHPUnit\Framework\isEmpty;

class AppointmentController extends Controller
{
    protected $appointmentRepository;
    protected $leadRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->leadRepository = new LeadRepository();
    }

    //TODO: sp2 is used to change the appointment time. Need to refactor this code so it looks/works better
    public function assignSp2(lead $lead, Appointment $appointment, Request $request)
    {
        $sp2 = User::where('id', $request->user_id)->first();
        if ($sp2) {
            $subject = $sp2->first_name . ' ' . mb_substr($sp2->last_name, 0, 1) . '. ' . $lead->customer->full_name;
        } else {
            $userLead = UserHasLead::where('lead_id', '=', $lead->id)
                ->where('position_id', '=', 3)
                ->with('user')
                ->get()
                ->first();
            $user = $userLead->user;
            if ($user) {
                $subject = $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '. ' . $lead->customer->full_name;
            } else {
                $subject = 'no sp2 yet ' . $lead->customer->full_name;
            }
        }

        $appointment->user_id = $request->user_id;
        $appointment->subject = $subject;
        $this->appointmentRepository->textNotifySP1ofSP2($lead, $request->user_id, $appointment->start_time);
//            return $this->appointmentRepository->textNotifySP2OfAssigned($lead, $request->user_id, $appointment->start_time);


        $appointment->save();


        if ($sp2) {
            $body = 'New appointment!';
            $link = URL::to('/') . '/dashboard/lead/' . $lead->id;

            Mail::to($sp2->email)
                ->queue(new BaseMailable('Appointment for ' . $lead->customer->first_name . '.', $body, $link));
            $newUser = UserHasLead::firstOrCreate(['user_id' => $request->user_id, 'lead_id' => $lead->id, 'position_id' => 3]);
        }
    }

    public function reorganizeBykey($objects, $keys)
    {
        $results = array();
        foreach ($keys as $key) {
            $i = 0;
            foreach ($objects as $object) {

                if ($object->office_id == $key) {
                    $results[$i] = $object->list;
                }
                $i++;
            }
        }
//        $others = (array_diff_assoc($objects,$results));
//        $results = array_merge($results,$others);
        return collect($results);
    }

    public function reorganizeByUsers($objects, $keys)
    {
        $results = array();
        foreach ($keys as $key) {
            $i = 0;
            foreach ($objects as $object) {

                if ($object->id == $key) {

                    $results[$i] = $object;
                }
                $i++;
            }
        }
        return collect($results);
    }

    function timeZone($lead)
    {
        switch ($lead->customer->state) {
            case 'NV':
            case 'CA':
                $timeZone = 'America/Los_Angeles';
                break;
            case 'TX':
                $timeZone = 'America/Chicago';
                break;
            case 'FL':
                $timeZone = 'America/New_York';
                break;
            default:
                Log::critical('Timezone not found for state: ' . $lead->customer->state);
                $timeZone = 'America/Denver';
                break;
        }
        return $timeZone;
    }


    public function AllAvailableAppointments(Lead $lead, Request $request)
    {
        if ($request->remote == 'true') {
            $remote = true;
        } else {
            $remote = false;
        }

        $utcOffSet = $this->timeZone($lead);
        $currentTime = Carbon::now()->timezone($utcOffSet);
        $currentTime->addDays($request->day)->startOfDay();

        $endTime = $currentTime->clone()->endOfDay()->utc();
        $currentTime->utc();

        $userIds = NewAppointmentHelper::getRRUserIds($lead);
        $users = NewAppointmentHelper::getUsers($userIds, $lead->customer->city, $currentTime, $endTime, $remote, $lead->customer, $lead->source);
        $users = $this->reorganizeByUsers($users, $userIds);
        $slotArray = NewAppointmentHelper::setSlots($currentTime, $endTime, $remote);
        $users = NewAppointmentHelper::removeUsersByLanguage($lead->customer, $users);

        $slotCollection = NewAppointmentHelper::checkAvailability($slotArray, $users);
        $slotCollection = NewAppointmentHelper::checkAppointments($slotCollection, $users);


        if (!$remote) {
            $slotCollection = NewAppointmentHelper::checkDistanceAndTime($slotCollection, $users, $lead->customer);
            $slotCollection = NewAppointmentHelper::removeUserFromDistance($slotCollection);
            $slotCollection = NewAppointmentHelper::castDistanceToArray($slotCollection);
        }
        return NewAppointmentHelper::removeAppointment($slotCollection);

    }

    public function assignSP1($lead, $apiUser)
    {
        $appointment = Appointment::where('lead_id', '=', $lead->id)
            ->where('user_id', '=', null)
            ->with('lead.office')
            ->first();

        if ($appointment == null) {
            return 'taken';
        }

        if ($apiUser->hasAnyRole(['executive', 'administrator'])) {
            $users = User::permission('accept sp2')
                ->where('terminated', null)
                ->with('office')->get();
        } elseif ($apiUser->hasRole('manager') && ($apiUser->office_id === 1 || $apiUser->office_id === 2)) {
            $users = User::where(function ($q) {
                $q->where('office_id', '=', 2);
                $q->orWhere('office_id', '=', 1);
            })->where('terminated', null)
                ->permission('accept sp2')
                ->get();
        } elseif ($apiUser->hasRole('manager')) {
            $users = User::hasOffice($apiUser->user_id)
                ->where('terminated', null)
                ->permission('accept sp2')
                ->get();
        }

        $appointmentTime = [Carbon::parse($appointment->start_time)->subMinutes(30), Carbon::parse($appointment->finish_time)->addMinutes(30)];
        foreach ($users as $i => $user) {
            $taken = Appointment::where('user_id', $user->id)
                ->whereBetween('start_time', $appointmentTime)
                ->count();
            if ($taken > 0) {
                unset($users[$i]);
            }
            $i++;
        }


        $usersList = User::whereIn('id', $users)->get();

        return UserResource::collection($users);
    }

    public function reassignList(Lead $lead, Appointment $appointment)
    {
        $apiUser = \Auth::user();

        $users = User::where('office_id', $lead->office_id)
            ->where('terminated', null)
            ->permission('accept sp2')
            ->get();

        $available = $users;

        $i = 0;
        $userArray = [];
        foreach ($available as $user) {
            $temp = [];
            $temp = ['label' => $user->full_name, 'value' => $user->id];
            $temp = collect($temp);
            $userArray[$i] = $temp;
            $i++;
        }

        $temp = ['label' => 'Select Someone', 'value' => null];
        $temp = collect($temp);
        array_unshift($userArray, $temp);

        return collect($userArray);

    }


    public function bookRR(Lead $lead, Request $request)
    {

        if ($request->remote) {
            $endTime = Carbon::parse($request->start_time)->addHour();
        } else {
            $endTime = Carbon::parse($request->start_time)->addHours(2);
        }
        $taken = Appointment::where('user_id', $request->userId)
            ->where('type_id', 6)
            ->whereBetween('start_time', [$request->start_time, $endTime])
            ->count();


        foreach ($request->userList as $key => $maybeUser) {
            $taken = Appointment::where('user_id', $maybeUser)
                ->where('type_id', 6)
                ->whereBetween('start_time', [$request->start_time, $endTime])
                ->count();
            if ($taken == 0) {
                $userId = $maybeUser;
                break;
            }
        }
        if ($taken) {
            return collect(['taken' => true]);

        }

        $user = User::where('id', $userId)->first();


        $subject = 'Close ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1)
            . '.  @' . $lead->customer->full_name;

        $appointment = Appointment::create([
            'user_id' => $userId,
            'lead_id' => $lead->id,
            'type_id' => 6,
            'created_by' => Auth::user()->id,
            'remote' => $request->remote ?? false,
            'subject' => $subject,
            'start_time' => $request->start_time,
            'finish_time' => $endTime
        ]);

        event(new AppointmentBookedEvent($appointment));

        $link = URL::to("/") . "/dashboard/lead/" . $lead->id;

        $newRep = new UserHasLead();
        $newRep->user_id = $user->id;
        $newRep->lead_id = $lead->id;
        $newRep->position_id = 3;
        $newRep->save();
        $rep = new LeadRepsResource($newRep);
        event(new RepAddedToLeadEvent($rep, $appointment->lead->id), true);
        Lead::where('id', $lead->id)
            ->update(['office_id' => $user->office_id]);
        $lead->refresh();

        $body = "You have been scheduled for a close. " . Carbon::parse($appointment->start_time)->setTimezone($user->timezone)
                ->format('D F jS g:i a') . "\nZip " . $appointment->lead->customer->zip_code .
            "\n Click the link below to view \n";


        HelperController::email('New Close scheduled (' . $lead->id . ') ', $body, $link, $user, 'Lead');
        TwilioSMSController::sendSms($user->phone_number, $body . ' ' . URL::to('/') . '/dashboard/lead/' . $appointment->lead->id);
        event(new CustomerAppointmentEvent($lead->customer->cell_phone, $lead->customer->id, $newRep->user_id, $appointment->start_time, Auth::user()));
        event(new UpdateZapierEvent($lead, 'appointment'));



        if ($lead->originOffice->feed_global) {
            $officeId = $lead->office_id;
            $this->updateRoundRobin(1, $lead->office_id, 'Call Center Offices');

            try {
                $this->updateRoundRobin($officeId, $user->id, 'Call Center Appointments');
            } catch (\Exception $e) {
//                    Log
                Log::critical('Round Robin Error: ' . $e->getMessage());
//                    Log verbose
                Log::critical('Round Robin Error: ', ['error' => $e->getMessage(), 'officeId' => $officeId,
                    'userId' => $user->id, 'type' => 'Call Center Appointments']);
            }
        } else {
            $this->updateRoundRobin($lead->origin_office_id, $user->id, 'Call Center Appointments');
        }

        return new LeadAppointmentResource($appointment);
    }

    private function updateRoundRobin($officeId, $id, $type)
    {
        $officeRoundRobin = RoundRobin::where('office_id', $officeId)->where('type', $type)->first();
        $officeRoundRobinArray = $officeRoundRobin->list;
        unset($officeRoundRobinArray[array_search($id, $officeRoundRobinArray)]);
        $officeRoundRobinArray = array_values($officeRoundRobinArray); // Re-index array
        $officeRoundRobinArray[] = $id;
        $officeRoundRobin->list = $officeRoundRobinArray; // Set the 'list' attribute
        $officeRoundRobin->save(); // Save the updated model
        $officeRoundRobin->refresh();
        return $officeRoundRobin;
    }

    public function index(Request $request, Lead $lead)
    {
        $apiUser = Auth::user();

        switch ($request->type) {
            case 'bookAnySP1':
                $sp2 = null;
                return $this->AllAvailableAppointments($sp2, $request->office_id);
            case 'bookSelfSP2':
                return $this->AllAvailableAppointments($request->user_id, $request->office_id);
            case 'assignsp1':
                return $this->assignSP1($lead, $apiUser);
            case 'updateAppointment':
                return $this->AllAvailableAppointments($request->user_id, $request->office_id);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Lead $lead
     * @param \Illuminate\Http\Request $request ,
     * @return string
     */
    public function store(Lead $lead, Request $request)
    {

//		$validatedData = $request->validate([
//			'lead_id' => 'required|max:4',
//			'user_id' => 'required|max:4',
//			'type_id' => 'required|max:2',
//			'subject' => 'required|max:140',
//		]);

        $user = Auth::user();
        $sp2 = null;
        $startTime = Carbon::createFromFormat('Y-m-d h:i a', $request->start, Auth::user()->timezone);
        $startTime->setTimezone('UTC');
        $sp2Name = '';
        switch ($request->type_id) {
            case 4:
                $requestId = 4;
                $subject = 'Site survey ' . $lead->customer->full_name;
                $userId = 1;
                $end = $startTime->clone()->addHours(2)->toDateTimeString();
                break;
            case 5:
                $requestId = 5;
                $subject = 'Install ' . $lead->customer->first_name . ' ' . $lead->customer->last_name;
                $end = $startTime->clone()->addHours(6)->toDateTimeString();
                $userId = 1;
                break;
            case 6:
                $requestId = 6;
                if ($user->can('closer')) {
                    if (in_array($lead->customer->language, $user->languages)
                        && (($user->office === 10 && $request->remote) || $user->office_id !== 10)) {
                        $sp2 = User::where('id', $request->user_id)->first();

                        $userCount = UserHasLead::where('lead_id', $lead->id)
                            ->where(function ($q) {
                                return $q
                                    ->where('position_id', 3)
                                    ->orWhere('position_id', 5);
                            })->get()
                            ->count();

                        if ($userCount === 0) {
                            $newRep = new UserHasLead();
                            $newRep->user_id = $sp2->id;
                            $newRep->lead_id = $lead->id;
                            $newRep->position_id = 3;
                            $newRep->save();
                            $rep = new LeadRepsResource($newRep);
                            event(new RepAddedToLeadEvent($rep, $lead->id, true));
                        }
                    }


                } else {
                    $sp2 = null;
                }

                if ($sp2) {
                    $userId = $sp2->id;
                    $subject = 'Close ' . $sp2->first_name . ' ' . mb_substr($sp2->last_name, 0, 1) . '.  @' . $lead->customer->full_name;
                    $sp2Name = $sp2->first_name . ' ' . $sp2->last_name;
                } else {
                    $userId = null;
                    $subject = 'Close @ ' . $lead->customer->full_name;
                    $sp2Name = 'no sp2 yet';
                }
                $end = $startTime->clone()->addHours(2)->toDateTimeString();
                break;
            case 7:
                $requestId = 7;
                $subject = 'Follow up ' . $lead->customer->first_name . ' ' . $lead->customer->last_name;
                $end = $startTime->clone()->addHours(1)->toDateTimeString();
                $userId = $request->user_id;
                break;
            case 9:
//                Moving it outside
                $requestId = 6;
                $userId = 3;
                $subject = 'Close Shane M.' . '.  @' . $lead->customer->full_name;
                $end = $startTime->clone()->addHours(2)->toDateTimeString();
                $creditPass = [2, 3, 5, 6, 9];
                if ($lead->source === 'call center' && in_array($lead->credit_status_id, $creditPass)) {
                    $this->leadRepository->callCenterCommission($lead, $user, 1, 2);
                    $this->leadRepository->callCenterCommission($lead, $user, 1, 1);
                }
        }

        $appointment = new Appointment();


        $appointment->user_id = $userId;
        $appointment->lead_id = $lead->id;
        $appointment->subject = $subject;
        $appointment->type_id = $requestId;
        $appointment->user_id = Auth::user()->id;
        $appointment->comment = $request->comment;
        $appointment->start_time = $startTime->toDateTimeString();
        $appointment->finish_time = $end;
        $appointment->remote = $request->remote;
        $appointment->save();
        if ($sp2) {
            event(new CustomerAppointmentEvent($lead->customer->cell_phone, $lead->customer->id, $sp2->id, $appointment->start_time, Auth::user()));
        }


        if ($request->type_id === 6) {
            $requestedSystem = RequestedSystem::where('lead_id', $lead->id)
                ->first();

            if (!$requestedSystem) {
                $requestedSystem = new RequestedSystem();
                $requestedSystem->lead_id = $lead->id;
                $requestedSystem->save();

            }
            if (!$requestedSystem->approved) {

//                if ($lead->source === 'call center') {
//                    $queue = new Line();
//                    $queue->requested_user_id = $user->id;
//                    $queue->lead_id = $lead->id;
//                    $queue->type = 'Call_Center_Appointment';
//                    $queue->urgent = false;
//                    $queue->save();
//                }


                $something = $requestedSystem->getChanges();
                $something['id'] = $requestedSystem->id;
                $data = collect($something);

                event(new RequestedSystemEvent($data, $lead->id, false));


            }

        }


        event(new LeadNewAppointment($appointment, $sp2Name));

        return new LeadAppointmentResource($appointment);

    }

    public function bookEvent(Request $request, Lead $lead)
    {
        $utcOffSet = $this->timeZone($lead);
        $startTime = Carbon::createFromFormat('Y-m-d h:i a', $request->time, $utcOffSet);
        $startTime = $startTime->setTimezone('UTC');
        $user = User::where('id', $request->userId)->first();


        switch ($request->type) {
            case 'close':
                $subject = 'Close ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '.  @' . $lead->customer->full_name;
                $typeId = 6;
                $end = $startTime->clone()->addHours(2);
                break;
            case 'follow-up':
                $subject = 'Follow up ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '.  For ' . $lead->customer->full_name;
                $typeId = 7;
                $end = $startTime->clone()->addHours(1);
                break;
            case 'task':
                $subject = 'Task for ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '.  For customer ' . $lead->customer->full_name;
                $typeId = 10;
                $end = $startTime->clone()->addHours(1);
                break;
        }

        $link = URL::to("/") . "/dashboard/lead/" . $lead->id;
        $appointment = Appointment::where('lead_id', $lead->id)->where('type_id', 6)->get();
        if (count($appointment) && $typeId === 6) {
            $appointment = $appointment[0];

            $appointment->update([
                'subject' => $subject,
                'comment' => $request->comment,
                'remote' => $request->remote,
                'user_id' => $user->id,
                'start_time' => $startTime->toDateTimeString(),
                'finish_time' => $end
            ]);
        } else {
            $appointment = new Appointment();
            $appointment->user_id = $user->id;
            $appointment->lead_id = $lead->id;
            $appointment->subject = $subject;
            $appointment->type_id = $typeId;
            $appointment->status_id = 1;
            $appointment->created_by = Auth::user()->id;
            $appointment->comment = $request->comment;
            $appointment->start_time = $startTime->toDateTimeString();
            $appointment->finish_time = $end;
            $appointment->remote = $request->remote;
            $appointment->comment = $request->comment ?? '...';
            $appointment->save();
        }


        if ($typeId === 6) {
            $closerPresent = UserHasLead::where('position_id', 3)
                ->where('lead_id', $lead->id)->get();
            if (count($closerPresent)) {
                $closerPresent = $closerPresent[0];

                $oldRep = User::where('id', $closerPresent->user_id)->first();
                $body = "You have been removed from lead $lead->id to improve the schedule";
                TwilioSMSController::sendSms($oldRep->phone_number, $body . ' ' . URL::to('/') . '/dashboard/lead/' . $lead->id);
                $closerPresent->update(['user_id' => $user->id]);
            } else {
                $newRep = new UserHasLead();
                $newRep->user_id = $user->id;
                $newRep->lead_id = $appointment->lead->id;
                $newRep->position_id = 3;
                $newRep->save();
            }

            event(new UpdateZapierEvent($lead, 'appointment'));


            $subject = 'Close ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '.  @' . $lead->customer->full_name;
            $body = "You have been scheduled for a close. " . Carbon::parse($appointment->start_time)->setTimezone($user->timezone)
                    ->format('D F jS g:i a') . "\nZip " . $appointment->lead->customer->zip_code .
                "\n Click the link below to view \n";


        } else if ($typeId === 7) {
            $subject = 'Follow up ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '.  For customer' . $lead->customer->full_name;
            $body = "You have been assigned a follow up. " . Carbon::parse($appointment->start_time)->setTimezone($user->timezone)
                    ->format('D F jS g:i a') .
                "\n Click the link below to view \n";
        } else {
            $subject = 'Task for ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '.  For customer ' . $lead->customer->full_name;
            $body = "You have been assigned a task. " . Carbon::parse($appointment->start_time)->setTimezone($user->timezone)
                    ->format('D F jS g:i a') . "\nZip " . $appointment->lead->customer->zip_code .
                "\n Click the link below to view \n";
        }


        event(new LeadNewAppointment($appointment, $user->fullName));
        HelperController::email($subject, $body, $link, $user, 'Lead');
        TwilioSMSController::sendSms($user->phone_number, $body . ' ' . URL::to('/') . '/dashboard/lead/' . $appointment->lead->id);

        return new LeadAppointmentResource($appointment);
    }


    public function bookD2dRR(Request $request, Lead $lead)
    {


        $remote = false;
        $utcOffSet = $this->timeZone($lead);
        $currentTime = Carbon::now()->timezone($utcOffSet);
        $currentTime->addDays($request->day)->startOfDay();


        $endTime = $currentTime->clone()->endOfDay()->utc();
        $currentTime->utc();


        $users = User::query();
//        set current time to users UTC offset


        $officeRoundRobin = RoundRobin::where('office_id', $lead->office_id)->where('type', 'Call Center Appointments')->first();
        $rrList = $officeRoundRobin->list;
        $userIds = $rrList;
        $users->whereIn('id', $rrList);

        $users->with([
            'availability' => function ($q) use ($currentTime, $endTime) {
                $q->whereBetween('start', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
                $q->where('type', 'in-person');
            },
            'appointments' => function ($q) use ($currentTime, $endTime) {
                $q->whereBetween('start_time', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
                $q->where('type_id', 6);
                $q->where('remote', false);
            },
            'appointments.lead.customer'
        ]);

        $users = $users->get();
        $users = $this->reorganizeByUsers($users, $userIds);

        $slotArray = NewAppointmentHelper::setSlots($currentTime, $endTime, $remote);
        $users = NewAppointmentHelper::removeUsersByLanguage($lead->customer, $users);
//return $users;
        $slotCollection = NewAppointmentHelper::checkAvailability($slotArray, $users);
        $slotCollection = NewAppointmentHelper::checkAppointments($slotCollection, $users);

        $slotCollection = NewAppointmentHelper::checkDistanceAndTime($slotCollection, $users, $lead->customer);
        $slotCollection = NewAppointmentHelper::removeUserFromDistance($slotCollection);
        $slotCollection = NewAppointmentHelper::castDistanceToArray($slotCollection);
        return $slotCollection = NewAppointmentHelper::removeAppointment($slotCollection);

    }

    public function updateStatus(Lead $lead, Appointment $appointment, Request $request)
    {

        if ($request->statusId === 1) {
            $time = null;

        } else {
            $time = Carbon::now()->toDateTimeString();

        }
        $appointment->update([
            'status_id' => $request->statusId,
            'completed_at' => $time
        ]);

        return new LeadAppointmentResource(Appointment::where('id', $appointment->id)->with('user', 'lead', 'lead.customer', 'createdBy')->first());

    }


    /**
     * Display the specified resource.
     * @param int $id
     * @return LeadAppointmentResource
     */
    public function show(Lead $lead, Appointment $appointment, Request $request)
    {
        $apiUser = Auth::user();
        if ($request->noUser == 1) {
            return new  LeadAppointmentResource(Appointment::where('lead_id', '=', $lead->id)->with('user', 'lead', 'lead.customer')->first());
        }
        return new LeadAppointmentResource(Appointment::where('lead_id', '=', $lead)->with('user', 'lead', 'createdBy')->get()->first());
    }

    function createGoogle($userEmail, $customer, $start, $end, $subject)
    {
        $googleAuth = new GoogleOAuth2();
        $client = $googleAuth->getClient($userEmail);
        $customerAddress = $customer->street_address . ' ' . $customer->city . ', ' . $customer->zip_code;

        $googleAuth->createEvent($client, $start, $end, $customerAddress, $subject,
            $customer->email, $userEmail);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Lead $lead
     * @param Appointment $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead, Appointment $appointment)
    {

        if ($request->changeUser === true) {

            $appointment->user_id = $request->newUser;
            $newUser = User::find($request->newUser);
            if ($appointment->type_id === 6) {
                $appointment->subject = 'Close ' . $newUser->first_name . ' ' . mb_substr($newUser->last_name, 0, 1) . '.  @' . $lead->customer->full_name;

                $appointment->user_id = $request->newUser;


                if ($lead->office_id === 10) {
                    $lead->office_id = $newUser->office_id;
                    $lead->save();
                }
                UserHasLead::where('position_id', 3)->where('lead_id', $lead->id)->delete();
                UserHasLead::firstOrCreate(['user_id' => $request->newUser, 'lead_id' => $lead->id, 'position_id' => 3]);

                $appointmentType = ' ';
                if ($appointment->remote) {
                    $appointmentType = 'a remote';
                } else {
                    $appointmentType = 'an in home';
                }
                $this->appointmentRepository->textNotifySP2OfAssigned($lead, $newUser->user_id, $appointment->start_time);
                $body = 'You have ' . $appointmentType . ' appointment with ' . $appointment->lead->customer->first_name . ' ' . $appointment->lead->customer->last_name .
                    ' @' . $appointment->lead->customer->street_address . '
                     '
                    . $appointment->lead->customer->zip_code . '   ';
                $link = URL::to('/') . '/dashboard/lead/' . $appointment->lead->id;
                HelperController::email('New Appointment (' . $lead->id . ')', $body, $link, $newUser, 'Appointment');
            }
        }
        if ($request->changeTime) {


            switch ($lead->customer->state) {
                case 'CA':
                    $timezone = 'America/Los_Angeles';
                    break;
                case 'TX':
                    $timezone = 'America/Chicago';
                    break;
                case 'FL':
                    $timezone = 'America/New_York';
                    break;
                default:
                    $timezone = 'America/Los_Angeles';
                    break;
            }

            if ($lead->origin_office_id === 55) {
                $timezone = 'America/Chicago';
            }

            $startTime = Carbon::createFromFormat('Y-m-d h:i a', $request->startTime, $timezone);
            $startTime->setTimezone('UTC');
            $appointment->start_time = $startTime->toDateTimeString();


            switch ($request->type) {
                case 1:
                    $appointment->finish_time = $startTime->addHours(1)->toDateTimeString();
                    break;
                case 2:
                    $appointment->finish_time = $startTime->addHours(3)->toDateTimeString();
                    break;
                case 3:
                    $appointment->finish_time = $startTime->addHours(2)->toDateTimeString();
                    break;
                case 4:
                    $appointment->finish_time = $startTime->addHours(1)->toDateTimeString();
                    break;
                case 5:
                    $appointment->finish_time = $startTime->addHours(1)->toDateTimeString();
                    break;
                default:
                    $appointment->finish_time = $startTime->addHours(2)->toDateTimeString();
                    break;
            }

        }


        $appointment->save();
//        event(new UpdateZapierEvent($lead, 'appointment'));
        return new LeadAppointmentResource(Appointment::where('id', '=', $appointment->id)
            ->with('user', 'lead', 'lead.leadNote', 'lead.customer', 'createdBy')
            ->first()
        );

        return new LeadAppointmentResource($appointment);


    }

    public function updateRemote(Request $request, Lead $lead, Appointment $appointment)
    {
        $appointment->remote = $request->remote;
        $appointment->save();
        return $appointment;

//            $appointment->
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead, Appointment $appointment)
    {

        $appointment->delete();
        event(new UpdateZapierEvent($lead, 'appointment'));

        if ($appointment) {
            return 1;
        } else {
            return 0;
        }
    }


    /**
     * Finds an available appointment
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function avalibleAppointment(Request $request)
    {
        return Appointment::whereHas('office', function ($q) {
            $q->where('office_id', '=', 1);
        })->get();
    }

    function periods($currentTime, $endTime)
    {

        $available = $currentTime->addHour();
        $available->minute(00)->second(0);
        $period = new CarbonPeriod($available, '30 minutes', $endTime);

        $i = 0;
        foreach ($period as $slot) {
            $newTime = clone $slot;
            $slotArray[$i]['start_time'] = $slot;
            $slotArray[$i]['end_time'] = $newTime->addMinutes(120);

            $i++;
        }
        return $slotArray;
    }

    public function reBook(Lead $lead, Appointment $appointment, Request $request)
    {
        $utcOffSet = $this->timeZone($lead);

        $currentTime = Carbon::now();
        $currentTime->addHours($utcOffSet);
        $currentTime->addDays($request->day)->startOfDay();
        $endTime = $currentTime->clone()->endOfDay();

//        dump([$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
        $between = [$currentTime, $endTime];
        $user = User::where('id', $appointment->user_id)
            ->with(['availability' => function ($q) use ($between) {
                $q->whereBetween('start', $between);
            },
                'appointments' => function ($q) use ($between) {
                    $q->where('start_time', $between);
                    $q->where('type_id', 6);
                }])->with(['availability' => function ($q) use ($currentTime, $endTime) {
                $q->whereBetween('start', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
            },
                'appointments' => function ($q) use ($currentTime, $endTime) {
                    $q->whereBetween('start_time', [$currentTime->toDateTimeString(), $endTime->toDateTimeString()]);
                    $q->where('type_id', 6);
                    $q->where('remote', false);
                }, 'appointments.lead.customer'])->get();
//        $slotArray = $this->periods($currentTime, $endTime);
        $slotArray = NewAppointmentHelper::setSlots($currentTime, $endTime, $appointment->remote);
        $user = NewAppointmentHelper::removeUsersByLanguage($lead->customer, $user);
        $slotCollection = AppointmentHelper::processSlotCollection($slotArray, $user, $lead, $appointment->remote);
        $slotCollection = NewAppointmentHelper::checkAvailability($slotArray, $user);
//        $users = NewAppointmentHelper::removeUsers($slotCollection, $users);
        $slotCollection = NewAppointmentHelper::checkAppointments($slotCollection, $user);
        $slotCollection = NewAppointmentHelper::checkDistanceAndTime($slotCollection, $user, $lead->customer);

        return $slotCollection = NewAppointmentHelper::removeAppointment($slotCollection);

        return $slotCollection = AppointmentHelper::processSlotCollection($slotArray, $user, $lead, $appointment->remote, $appointment->id);

        $payload = array();
        foreach ($slotCollection as $key => $slot) {
            if (isset($slot['count'])) {
                if ($slot['count'] > 0 && isset($slot['userId']) && !empty($slot['userList'])) {
                    $slot['key'] = $key;
                    array_push($payload, $slot);
                }
            }
        }
//        event(new UpdateZapierEvent($lead, 'appointment'));

        return $payload;

    }

    public function updateRebook(Lead $lead, Appointment $appointment, Request $request)
    {
        $time = $this->bookRRTimeZone($lead, $request->start_time, $appointment->remote);

        $appointment->update([
            'start_time' => $time['startTime'],
            'finish_time' => $time['endTime']
        ]);

        $appointment = Appointment::where('id', $appointment->id)
            ->with('lead.customer', 'user', 'createdBy')->first();
//        event(new UpdateZapierEvent($lead, 'appointment'));

        return new LeadAppointmentResource($appointment);


    }

    function bookRRTimeZone($lead, $startTime, $remote)
    {
//        switch ($lead->customer->state) {
//            case 'CA':
//                $timezone = 'America/Los_Angeles';
//                break;
//            case 'TX':
//                $timezone = 'America/Chicago';
//                break;
//            case 'FL':
//                $timezone = 'America/New_York';
//                break;
//            default:
//                $timezone = 'America/Chicago';
//                break;
//        }


        $startTime = Carbon::parse($startTime);

        if ($remote) {
            $endTime = $startTime->copy()->addHours(1);
        } else {
            $endTime = $startTime->copy()->addHours(2);
        }
        return collect(['startTime' => $startTime, 'endTime' => $endTime]);
    }
}

