<?php


namespace App\Http\Controllers\Api\SalesFlow\Calender;


use App\Http\Resources\AvailabilityResource;
use App\Models\Office\Office;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Appointment\Availability;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController
{
    public function index(Request $request)
    {
//        return $request;

        $officeId =$request->office_id;

        if ($request->type = 'office') {
            $availableReps = Availability::where('start', '>', Carbon::now())->with('user')
                ->whereHas('user.officeHasUser', function ($q) use($officeId) {
                    $q->where('office_id', '=', $officeId);
                })->get();

            return AvailabilityResource::collection($availableReps);
        }
        return AvailabilityResource::collection(Availability::where('user_id', '=', $request->user_id)->get());
    }

    public function store(Request $request)
    {

        $availability          = new Availability();
        $availability->start   = Carbon::parse($request->start);
        $availability->end     = Carbon::parse($request->end);
        $availability->type = $request->type;
        $availability->user_id = $request->user_id;
        $availability->save();


        return new AvailabilityResource($availability);
    }

    public function update(Request $request, Availability $availability)
    {
        $availability->start    = Carbon::parse($request->start);
        $availability->end      = Carbon::parse($request->end);
        $availability->approved = $request->approved;
        $availability->save();

        $status = ['ok' => true];
        return collect($status);
    }

    public function show(Availability $availability)
    {
        return new AvailabilityResource($availability);
    }

}
