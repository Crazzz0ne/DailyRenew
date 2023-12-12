<?php

namespace App\Http\Controllers\Api\SalesFlow\Lead;

use App\Events\Backend\SalesFlow\Lead\JeopardyEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateEvent;
use App\Events\Backend\SalesFlow\Lead\LeadUpdateTwoEvent;
use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\QueuePageEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Lead\LeadDashboardRequest;
use App\Http\Resources\Lead\LeadStatusResource;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\LeadsResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\LeadStatus;
use App\Models\SalesFlow\Lead\Line;

use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\System\System;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Slack;
use Torann\GeoIP\Facades\GeoIP;

class LeadController extends Controller
{

    protected $leadRepository;

    public function __construct(Lead $lead)
    {
        // set the model
        $this->leadRepository = new LeadRepository();
    }

    public function updateLeadStatus(Request $request, Lead $lead)
    {
        $user = \Auth::user();

        $lead->status_id = $request->status;
        $lead->jeopardy_id = null;
        $lead->save();
//        create a lead note of the new status id by the auth user.
//        refresh lead
        $lead->refresh();
        $leadNote = new LeadNote();
        $leadNote->lead_id = $lead->id;
        $leadNote->user_id = $user->id;
        $statusName = LeadStatus::where('id', $lead->status_id)->first();

        $leadNote->note = 'Status changed to ' . $statusName->name;
        $leadNote->save();

        $something['id'] = $lead->id;
        $something['status'] = $statusName->name;
        $data = collect($something);
        event(new LeadUpdateTwoEvent($lead));
        event(new LeadUpdateEvent($data, $lead->id));
        return $lead;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Lead $lead
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(LeadDashboardRequest $request)
    {
//        connect to google api for maps to find distance



        $start_time = microtime(true);
        $user = \Auth::user();

        if (!$user->timezone)
        {
            $location = GeoIP::getLocation(request()->ip());
            $user->timezone = $location->timezone;
            $user->save();
        }
        $validated = $request->validated();
//        TODO this needs to be optimized.
        $payload = [];
        $lead = Lead::query();
        $userId = $request->input('userId');

        $length = $validated['length'];
        $dir = $validated['dir'];
        $rawSearchValue = $validated['search'];
        $officeId = $validated['officeId'];
        $lowUsage = $validated['lowUsage'];
        $passedIntegrations = $validated['passedIntegrations'];
        $jij = $validated['jij'];
        $sat = $validated['sat'];
        $isClosed = $validated['closed'];
        $creditPass = $validated['creditPass'];
        $status = $validated['status'];
        $selectedUser = $validated['selectedUser'];
        $appointment = $validated['appointment'];
        $projectManager = $validated['projectManager'];
        $callCenter = $validated['callCenter'];
        $inHouse = $validated['inHouse'];
        $selectedRegion = $validated['regionId'];
        $appointmentSet = $validated['appointmentSet'];
        $permitApproved = $validated['permitApproved'];
        $stale = $validated['stale'];
        $key = '';

        if ($rawSearchValue) {
            $key .= 'search.' . $rawSearchValue;
            $searchValues = array_map('trim', explode(',', $rawSearchValue));


            $names = explode(" ", $rawSearchValue);
            if (!empty($status)) {
                $lead->whereIn('status_id', $status)
                    ->whereHas('customer', function ($query) use ($rawSearchValue, $names) {
                        $query->where('street_address', 'like', '%' . $rawSearchValue . '%')
                            ->orWhere('cell_phone', 'like', '%' . $rawSearchValue . '%')
                            ->orWhere('zip_code', 'like', '%' . $rawSearchValue . '%')
                            ->orWhere('city', 'like', '%' . $rawSearchValue . '%')
                            ->orWhere('email', 'like', '%' . $rawSearchValue . '%')
                            ->orWhere('first_name', 'like', '%' . $names[0] . '%')
                            ->orWhere('last_name', 'like', '%' . $names[0] . '%');
                    });
                $key .= 'status.' . implode(',', $status);
            } else {
                $lead->whereHas('customer', function ($query) use ($rawSearchValue, $names) {
                    $query->where('street_address', 'like', '%' . $rawSearchValue . '%')
                        ->orWhere('cell_phone', 'like', '%' . $rawSearchValue . '%')
                        ->orWhere('zip_code', 'like', '%' . $rawSearchValue . '%')
                        ->orWhere('city', 'like', '%' . $rawSearchValue . '%')
                        ->orWhere('email', 'like', '%' . $rawSearchValue . '%')
                        ->orWhere('first_name', 'like', '%' . $names[0] . '%')
                        ->orWhere('last_name', 'like', '%' . $names[0] . '%');
                });
            }

            $lead->Orwhere(function ($query) use ($rawSearchValue) {
                $query->where('id', 'like', '%' . $rawSearchValue . '%');
            });

        } else {
            if (!empty($status)) {
                $key .= 'status.' . implode(',', $status);
                $lead->whereIn('status_id', $status);
            }
        }
        if ($selectedUser) {
            $key .= '.$selectedUser.' . $selectedUser;
            $lead->hasUser($selectedUser);
        }

        if ($passedIntegrations) {
            $key .= '.integrations.1';
            $lead->where('integrations_approved', 3);
        }
        if ($jij) {
            $key .= '.jij.1';
            $lead->isJobInJeopardy();
        }
        if ($sat) {
            $key .= '.sat.1';
            $lead->isSatSearch(true);
        }
        if ($creditPass) {
            $key .= '.credit.1';
            $lead->isCreditPass(true);
        }
        if ($lowUsage) {
            $key .= '.lowUsage.1';
            $lead->where('integrations_approved', 2);
        }
        if ($isClosed) {
            $key .= '.closed.1';
            $lead->isClosed();
        }
        if ($callCenter) {
            $key .= '.cc.1';
            $lead->where('source', 'call center');
        }
        if ($inHouse) {
            $key .= '.ih.1';
            $lead->where('source', '!=', 'call center');
        }
        if ($appointmentSet) {
            $key .= '.iapC.1';
            $lead->whereHas('appointments', function ($q) {
                $q->where('type_id', 6);
            });
        }
        if ($projectManager) {
            $key .= '.UserByPosition.' . Auth::user()->id . '.9';
            $lead->hasUserByPosition($userId);
        }
        if (!$stale) {
            $lead->where('stale', null);
        } else {
            $key .= '.stale';
            $lead->where('stale', '!=', null);
        }
        if ($permitApproved) {
            $key .= '.permitApproved';
            $lead->whereHas('salesPacket', function ($q) {
                $q->where('permitting_received_date', '!=', null);
            });
        }

//        allow for admin to see all leads
        $this->getRoleBasedCacheKeyAndLead($user, $key, $lead);
        $this->handleOfficeId($user, $key, $lead, $officeId);
        $this->handleRegion($user, $key, $lead, $selectedRegion);
        $key .= 'length.' . $length . '.page.' . $request->page;

        $lead->with('customer', 'user', 'reps', 'office', 'leadUploads', 'appointments')
            ->orderBy('id', $dir);


        return LeadDashBoardResource::collection($lead->paginate($length));

    }

    private function getRoleBasedCacheKeyAndLead($user, &$key, $lead)
    {
        if ($user->hasAnyRole(['executive', 'administrator', 'proposal builder'])) {
            $key .= 'admin.';
        } elseif ($user->hasRole('regional manager')) {
            $key .= 'regionalFilterCache.' . $user->office()->first()->region_id;
            $officeArray = $this->regionalFilterCache($user->office_id, $key);
            $lead->whereIn('office_id', $officeArray);
        } elseif ($user->hasRole('manager') || $user->hasRole('integrations')) {
            $key .= 'managerFilterCache.' . $user->office_id;
            $lead->hasOffice($user->office_id);
        } else {
            $key .= 'user.' . $user->id;
            $lead->hasUser($user->id);
        }
    }

    private function handleOfficeId($user, &$key, $lead, $officeId)
    {
        if ($user->hasAnyRole(['executive', 'administrator', 'proposal builder', 'regional manager'])) {
            if ($officeId) {
                $key .= '.selectedOffice.' . $officeId;
                $lead->hasOffice($officeId);
            }
        }
    }

    private function handleRegion($user, &$key, $lead, $selectedRegion)
    {
        if ($user->hasRole('regional manager')) {
            $office = Office::where('id', $user->office_id)->first();
            $lead->hasRegion($office->market_id);
            $key .= '.selectedRegion.' . $office->region_id;
        } else {
            if ($selectedRegion) {
                $lead->hasRegion($selectedRegion);
                $key .= '.selectedRegion.' . $selectedRegion;
            }
        }
    }

    public function closedCount(Request $request)
    {

        $user = \Auth::user();

        $userId = $request->input('userId');

        $officeId = $request->input('officeId');
        $selectedUser = $request->input('selectedUser');
        $selectedRegion = $request->input('regionId');
        $lead = Lead::query();

        if ($user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'regional manager'])) {
            if ($officeId) {
                $lead->hasOffice($officeId);
            } elseif ($user->hasAnyRole(['regional manager'])) {
                $regionalOfficeID = $user->office_id;
                $marketId = Office::where('id', $regionalOfficeID)->get()->pluck('market_id');
                $offices = Office::where('market_id', $marketId)->get()->pluck('id')->toarray();
                $lead->whereIn('office_id', $offices);
            } elseif ($selectedRegion) {
                $lead->hasRegion($selectedRegion);
            }
        }
        if ($selectedUser) {
            $lead->hasUser($selectedUser);
        }
        if ($user->hasRole('manager') && (!$user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'regional manager']))) {
            $lead->hasOffice($user->office_id);
        }
        if (!$user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'manager'])) {
            $lead->hasUser($userId);
        }
//        $lead->whereHas('salesPacket', function ($q) {
//            $q->where('cpuc_doc_signed', '>', Carbon::now()->startOfMonth()->timezone('America/Los_Angeles')->tz('UTC')->toDateString());
//        });
        $lead->where('close_date', '>=', Carbon::now()->startOfMonth()->timezone('America/Los_Angeles')->tz('UTC')->toDateString());

        return $lead->count();
    }

    public function Closed(Request $request)
    {
        $start_time = microtime(true);
        $user = \Auth::user();

//        TODO this needs to be optimized.
        $key = '';

        $lead = Lead::query();
        $userId = $request->input('userId');


        $length = $request->input('length', 10);
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $rawSearchValue = $request->input('search');
        $officeId = $request->input('officeId');

        $status = $request->input('status');
        $selectedUser = $request->input('selectedUser');

//        $callCenter = $request->input('callCenter');
//        $inHouse = $request->input('inHouse');
        $selectedRegion = $request->input('regionId');
//        $missedOpp = $request->input('missedOpp');


        if ($rawSearchValue) {
            $key = 'dashboard.search' . $rawSearchValue;
            $searchValues = array_map('trim', explode(',', $rawSearchValue));

            foreach ($searchValues as $searchValue) {

                $names = explode(" ", $searchValue);

                $lead->whereHas('customer', function ($query) use ($searchValue, $names) {
                    $query->where('street_address', 'like', '%' . $searchValue . '%')
                        ->orWhere('home_phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('cell_phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('zip_code', 'like', '%' . $searchValue . '%')
                        ->orWhere('city', 'like', '%' . $searchValue . '%')
                        ->orWhere('spouse_name', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('first_name', 'like', '%' . $names[0] . '%')
                        ->orWhere('last_name', 'like', '%' . $names[0] . '%');
                });


                $lead->Orwhere(function ($query) use ($searchValue) {
                    $query->where('id', 'like', '%' . $searchValue . '%');
                });

            }
        }

        if (!$user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'manager', 'account manager'])) {
            $lead->hasUser($userId);
        }
        $key .= 'userId' . $userId;
        if ($user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'regional manager', 'account manager'])) {

            if ($officeId) {
                $key .= 'selectedOffice' . $officeId;
                $lead->hasOffice($officeId);
            } elseif ($user->hasAnyRole(['regional manager'])) {
                $regionalOfficeID = $user->office_id;
                $marketId = Office::where('id', $regionalOfficeID)->get()->pluck('market_id');
                $offices = Office::where('market_id', $marketId)->get()->pluck('id')->toarray();
                $lead->whereIn('office_id', $offices);
                $key .= 'selectedOffice' . $officeId;
            } elseif ($selectedRegion) {
                $key .= 'selectedRegion' . $selectedRegion;
                $lead->hasRegion($selectedRegion);
            }
        }
        if ($selectedUser) {
            $key .= 'selectedUser' . $selectedUser;
            $lead->hasUser($selectedUser);
        }
        if ($user->hasRole('manager') && (!$user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'regional manager', 'account manager']))) {
            $lead->hasOffice($user->office_id);
        }


        $lead->with('customer', 'user', 'reps', 'office')->where('close_date', '!=', null)->orderBy('close_date', $dir);
//       return $lead->toSql();

//        $end_time = microtime(true);
//        $execution_time = ($end_time - $start_time);
//        $log = 'run time LeadController@index(' . $length . ')-' . $user->first_name;
        $key .= 'length.' . $length . '.page.' . $request->page;
        $payload = Cache::remember($key, 30, function () use ($lead, $length) {
            return $lead->paginate($length);
        });

//        if ($execution_time >= 0.1) {
//            \Log::info($log, [$execution_time]);
//        }

        return LeadsResource::collection($payload);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $lead = Lead::where('id', $request->leadId)->first();
        return $this->leadRepository->createNewLead($lead);

    }

    /**
     * Display the specified resource.
     * @param Lead $lead
     * @param Request $request
     * @return LeadsResource
     */
    public function show(Lead $lead, Request $request)
    {


        $start_time = microtime(true);
        $user = \Auth::user();

        $userHasLead = UserHasLead::where('lead_id', $lead->id)
            ->where('user_id', $user->id)
            ->get()
            ->count();

        if ($user->hasAnyRole(['administrator', 'executive', 'integrations', 'proposal builder', 'account manager', 'pre sale'])) {

        } elseif ($user->hasRole('integrations') && ($lead->office_id === 1 || 1 === $lead->origin_office_id)) {


        } elseif ($user->hasRole('regional manager')) {
            $userMarketId = Office::where('id', $user->office_id)->pluck('market_id')->first();
            $leadMarketId = Office::where('id', $lead->office_id)->pluck('market_id')->first();

            if ($leadMarketId !== $userMarketId) {
                Mail::to('chris.furman@solcalenergy.com')
                    ->queue(new BaseMailable('Someone went where they shouldnt', $user, 'nowhere', 'lead'));

                return 'nope';
            }

        } else if ($userHasLead) {

        } else if ($user->hasRole('manager') && ($user->office_id === $lead->office_id || $user->office_id === $lead->origin_office_id)) {

        } else {


            return 'nope';
        }

//        $end_time = microtime(true);
//        $execution_time = ($end_time - $start_time);
//        $log = 'run time LeadController@show(' . $lead->id . ')-' . $user->first_name;

//        if ($execution_time > 0.5) {
//            \Log::info($log, [$execution_time]);
//        }

        return new LeadsResource(Lead::where('id', '=', $lead->id)
            ->with('reps', 'salesPacket', 'appointments.user', 'customer', 'leadRoof', 'siteSurveyQuestions', 'system', 'appointments')
            ->first());

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    public function status()
    {

        return LeadStatusResource::collection(LeadStatus::all());
    }


    public function update(Request $request, Lead $lead)
    {
        $user = \Auth::user();

        $lead->update($request->except(['usageReview', 'creditReview']));


//        gets only the changes
        $something = $lead->getChanges();
        if (count($something) > 0) {
///Integrations

            if ($request->creditReview) {
//                check to see if lead->office->market  === 1


                //Sets credit as a pass
                if ($lead->status_id === 1 || $lead->status_id === 2 || $lead->status_id === 14 || $lead->status_id === 3
                    || $lead->status_id === 4 || $lead->status_id === 15) {

                    if ($lead->isCreditPass()) {
                        $lead->status_id = 3;
                        $lead->save();
                    }  else {
                      if ($lead->credit_status_id === 7){
                          $lead->status_id = 15;
                          $lead->save();
                      }

                    }
                }
                event(new UpdateZapierEvent($lead, 'credit'));

            }

//        I need the ID for vue to match on the page
            $statusName = LeadStatus::where('id', $lead->status_id)->first();

            $something['id'] = $lead->id;
            $something['status'] = $statusName->name;
            $data = collect($something);

//        lets everyone else know of the changes
            event(new LeadUpdateTwoEvent($lead));
            event(new LeadUpdateEvent($data, $lead->id));
        }
        return $something;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Lead $lead
     * @return Response
     */


    public function emailAlertPB($subject, $body, $link, $reps)
    {


        foreach ($reps as $rep) {

            Mail::to($rep->email)
                ->queue(new BaseMailable($subject, $body, $link, 'Proposal'));


        }

        return 'Sms Sent';
    }



    public function jeopardy(Lead $lead, Request $request)
    {

        if (!$lead->jeopardy_id) {


            $lead->jeopardy_id = $lead->status_id;
            $lead->status_id = $request->reason;
            $lead->save();

            $reasonName = LeadStatus::where('id', $request->reason)->first();

            $note = new LeadNote();
            $note->note = $reasonName->name . ' : ' . $request->note;
            $note->lead_id = $lead->id;
            $note->user_id = $request->userId;

            $note->save();

            $customer = $lead->customer;
            $subject = $customer->first_name . ' ' . $customer->last_name . ' is ' . $reasonName->name;
            $body = $note->note;

            $link = URL::to('/') . '/dashboard/lead/' . $lead->id;
            $reps = UserHasLead::where('lead_id', '=', $lead->id)
                ->get();

            $duplicate = [];
//            foreach ($reps as $rep) {
//                $send = in_array($rep->user_id, $duplicate);
//                if (!$send) {
//                    array_push($duplicate, $rep->user_id);
//                    $user = User::where('id', $rep->user_id)->get()->first();
//                    $this->leadRepository->email($subject, $body, $link, $user);
//                }
//            }
//New event JeopardyEvent

//            $shane = User::where('email','shane@solarbrightwave.com')->get()->first();
//
//            $this->leadRepository->email($subject, $body, $link, $shane);

            if ($lead->status_id == 14 || $lead->status_id == 20) {
                event(new JeopardyEvent($lead));

                $salesPacket = SalesPacket::where('id', $lead->sales_packet_id)->first();
                $salesPacket->sat = false;
                $salesPacket->save();
            }
            if ($lead->status_id === 23) {
                SalesPacket::where('id', $lead->salesPacket_id)->update(['sat' => true]);
            }

//            $birdDogs = User::permission('accept proposal builder')->get();

//            foreach ($birdDogs as $birdDog) {
//                Mail::to($birdDog->email)
//                    ->queue(new BaseMailable($subject, $body, $link, 'lead'));
//            }


        } else {
            $lead->status_id = $lead->jeopardy_id;
            $lead->jeopardy_id = null;
            $lead->save();

            $note = new LeadNote();
            $note->note = 'Job is no longer in jeopardy';
            $note->lead_id = $lead->id;
            $note->user_id = $request->userId;

            $note->save();

            $customer = $lead->customer;
            $subject = $customer->first_name . ' ' . $customer->last_name . ' is no longer in Jeopardy';
            $body = $note->note;

            $link = URL::to('/') . '/dashboard/lead/' . $lead->id;
            $reps = UserHasLead::where('lead_id', '=', $lead->id)->get();
            $duplicate = [];
            foreach ($reps as $rep) {
                $send = in_array($rep->user_id, $duplicate);
                if (!$send) {
                    array_push($duplicate, $rep->user_id);
                    $user = User::where('id', $rep->user_id)->get()->first();
                    $this->leadRepository->email($subject, $body, $link, $user);
                }
            }

//            $shane = User::where('email', 'shane@solarbrightwave.com')->get()->first();

//            $this->leadRepository->email($subject, $body, $link, $shane);

        }
        $something = null;

        $something = $lead->getChanges();
        $something['id'] = $lead->id;
        $something['status'] = $lead->statusName->name;
        $data = collect($something);


//        lets everyone else know of the changes
        event(new LeadUpdateEvent($data, $lead->id));


//        return $this->leadRepository->showLead($lead);

    }


    public function leadHasUserByPosition($leadId, $positionID)
    {

        return User::whereHas("leads", function ($q) use ($positionID, $leadId) {
            $q->where("position_id", "=", $positionID);
            $q->where("lead_id", '=', $leadId);
        })->get();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Lead $lead, Request $request)
    {


        $user = \Auth::user();
        if ($user->can('administrate company')) {
            Line::where('lead_id', $lead->id)->delete();
            Appointment::where('lead_id', $lead->id)->delete();
            RequestedSystem::where('lead_id', $lead->id)->delete();
            ProposedSystem::where('lead_id', $lead->id)->delete();
            SalesPacket::where('id', $lead->sales_packet)->delete();
            LeadNote::where('lead_id', $lead->id)->delete();
            LeadLogin::where('lead_id', $lead->id)->delete();
            System::where('id', $lead->system_id)->delete();
            UserHasLead::where('lead_id', $lead->id)->delete();
            Customer::where('id', $lead->customer_id)->delete();
            $lead->delete();
        }
    }
}
