<?php

namespace App\Http\Controllers\Api\SalesFlow\Calender;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadsResource;
use App\Models\Office\Office;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostAppointmentController extends Controller
{

    public function index(Request $request)
    {
        $start_time = microtime(true);
        $user = \Auth::user();
        $userId = $request->input('userId');
        $selectedDate = $request->input('selectedDate');
        $length = $request->input('length', 10);

        $lead = Lead::query()
            ->when(!$user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'manager']), function ($query) use ($userId) {
                return $query->hasUser($userId);
            })
            ->when($user->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder', 'regional manager']), function ($query) use ($request, $user) {
                return $query->when($request->input('officeId'), function ($query) use ($request) {
                    return $query->hasOffice($request->input('officeId'));
                })->when($user->hasRole('regional manager'), function ($query) use ($user) {
                    // Your regional manager logic here
                })->when($request->input('regionId'), function ($query) use ($request) {
                    return $query->hasRegion($request->input('regionId'));
                });
            })
            ->when($request->input('selectedUser'), function ($query) use ($request) {
                return $query->hasUser($request->input('selectedUser'));
            })
            ->when($user->hasRole('manager'), function ($query) use ($user) {
                return $query->hasOffice($user->office_id);
            })
            ->whereHas('appointments', function ($q) use ($selectedDate) {
                $q->where('start_time', '>=', Carbon::parse($selectedDate, 'America/Los_Angeles')->startOfDay()->tz('UTC')->toDateTimeString())
                    ->where('start_time', '<=', Carbon::parse($selectedDate, 'America/Los_Angeles')->endOfDay()->tz('UTC')->toDateTimeString())
                    ->where('type_id', 6);
            })
            ->with(['customer', 'user', 'reps', 'office', 'salesPacket', 'appointments'])
            ->orderBy('id', 'asc');

        $end_time = microtime(true);
        $execution_time = ($end_time - $start_time);
        $log = 'run time LeadController@index(' . $length . ')-' . $user->first_name;

        if ($execution_time >= 0.05) {
            \Log::info($log, [$execution_time]);
        }

        return LeadsResource::collection($lead->get());
    }
}
