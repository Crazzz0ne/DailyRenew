<?php

namespace App\Http\Controllers\Api\SalesFlow\Calender;

use App\Http\Controllers\Controller;
use App\Http\Resources\CalenderResource;
use App\Http\Resources\LeadAppointmentResource;
use App\Http\Resources\LeadCollection;
use App\Http\Resources\LeadsResource;
use App\Models\Auth\User;
use App\Models\Office\Market\Market;
use App\Models\Office\Office;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CalenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function store(Request $request)
    {


        $user = Auth::user();
        $key = 'calendar.start.' . $request->start . '.end.' . $request->end;

        $start = Carbon::parse($request->start)->toDateTimeString();
        $startTime = Carbon::parse($start, $user->timezone)->setTimezone('UTC');
        $end = Carbon::parse($request->end)->toDateTimeString();
        $endTime = Carbon::parse($end, $user->timezone)->setTimezone('UTC');

        $calender = Appointment::query();
        $range = [$startTime->todateTimeString(), $endTime->todateTimeString()];

        $calender->whereBetween('start_time', $range);

        if ($request->type) {
            $calender->where('type_id', $request->type);
            $key .= '.calendar.type.' . $request->type;
        }

        $selectedUser = $request->selectedUser;
        $selectedOffice = $request->selectedOffice;
        $selectedRegion = $request->selectedRegion;

        if ($user->hasAnyRole(['administrator', 'executive', 'proposal builder', 'account manager'])){
            if ($selectedUser) {
                $key .= '.user.' . $this->applyUserFilter($calender, $selectedUser);
            } else if ($selectedOffice) {
                $key .= '.office.' . $this->applyOfficeFilter($calender, $selectedOffice);
            } else if ($selectedRegion) {
                $key .= '.region.' . $this->applyRegionFilter($calender, $selectedRegion);
            }
        } elseif ($user->hasAnyRole(['regional manager'])) {
            if ($selectedUser) {
                $key .= '.user.' . $this->applyUserFilter($calender, $selectedUser);
            } else if ($selectedOffice) {
                $key .= '.office.' . $this->applyOfficeFilter($calender, $selectedOffice);
            } else {
                $key .= '.region.' . $this->applyRegionFilter($calender, $selectedRegion);
            }
        } elseif ($user->hasAnyRole(['manager'])) {
            if ($selectedUser) {
                $key .= '.user.' . $this->applyUserFilter($calender, $selectedUser);
            } else {
                $key .= '.office.' . $this->applyOfficeFilter($calender, $user->office_id);
            }
        } else {
            $key .= '.user.' . $this->applyUserFilter($calender, $user->id);
        }

        if ($request->source == 'all') {
            $key .= '.source.all';
        } elseif ($request->source === 'call-center') {
            $calender->whereHas('lead', function ($q) {
                $q->where('source', 'call center');
            });
            $key .= '.source.call-center';
        } else {
            $calender->whereHas('lead', function ($q) {
                $q->where('source', '!=', 'call center');
            });
            $key .= '.source.' . $request->source;
        }

        $payload = Cache::remember($key, 30, function () use ($calender, $range) {
            return $calender->with(['lead', 'lead.leadUploads' => function ($q) {
                $q->where('type', 'bill');
            }])->get();
        });

        return CalenderResource::collection($payload);

    }

    function applyUserFilter($calender, $selectedUser)
    {
        $calender->where(function ($query) use ($selectedUser) {
            $query->whereHas('lead.reps', function ($q) use ($selectedUser) {
                $q->where('user_id', $selectedUser);
            });
        });

        return '.user.' . $selectedUser;
    }

    function applyOfficeFilter($calender, $officeId)
    {
        $calender->where(function ($query) use ($officeId) {
            $query->ofOffice($officeId);
        });

        return '.office.' . $officeId;
    }



    function applyRegionFilter($calender, $regionId)
    {
        $calender->where(function ($query) use ($regionId) {
            $query->whereHas('lead.office', function ($q) use ($regionId) {
                $q->where('market_id', $regionId);
            })->orWhereHas('lead.originOffice', function ($q) use ($regionId) {
                $q->where('market_id', $regionId);
            });
        });

        return '.rm.' . $regionId;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request ,
     * @return Response
     */


    /**
     * Display the specified resource.
     * @param int $id
     * @return Response
     */
    public function showByLeadId($id)
    {
        $appointment = Appointment::where('lead_id', '=', $id)->where('start_time', '>', Carbon::now())->get()->first();
        if ($appointment) {
            return 1;
        } else {
            return 0;
        }
        return new LeadAppointmentResource();
    }


    /**
     * Display the specified resource.
     * @param int $id
     * @return LeadAppointmentResource
     */
    public function show($id)
    {

        return new LeadAppointmentResource(Appointment::where('id', '=', $id)
            ->with('user', 'lead', 'lead.leadNote', 'lead.customer', 'createdBy')
            ->get()
            ->first()
        );
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

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return 'here';
        $appointment = Appointment::where('id', '=', $id)->get()->first();
        $appointment->lead_id = $request->lead_id;
        $appointment->user_id = $request->user_id;
        $appointment->type_id = $request->type_id;
        $appointment->subject = $request->subject;
        $appointment->comment = $request->comment;
        $appointment->start_time = $request->start_time;
        $appointment->finish_time = $request->finish_time;

        $appointment->save();

        return 'success';

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $delete = Appointment::where('id', '=', $id)->delete();
        if ($delete) {
            $status = ['ok' => true];
            return collect($status);
        } else {
            $status = ['ok' => false];
            return collect($status);
        }
    }
}
