<?php

namespace App\Http\Controllers\Api\SalesFlow\Reporting;

use App\Http\Controllers\Controller;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
class KPIRunChartController extends Controller
{

    private function init($request){
        $typeId = $request->{$request->scope . 'Id'};
    return "kpi.run-chart.type.$request->scope.id.$typeId.by.{$request->by}.grouping.{$request->grouping}";
    }
    public function index(Request $request)
    {
        $key = $this->init($request);
        $payload = Cache::remember($key, 43200, function () use ($request) {
            $between = $this->between(Carbon::now()->startOfYear()->timezone(7)->toDateTimeString(), Carbon::now(7));
            $leads = Lead::query();

            $field = $request->by === 'createdAt' ? 'created_at' : 'start_time';

            $leads->whereHas('appointments', function ($q) use ($between, $field) {
                $q->where('type_id', 6);
                $q->whereBetween($field, $between);
                $q->whereNull('deleted_at');
            });

            $scopeId = $request->{$request->scope . 'Id'};
            if (method_exists($leads, 'has' . ucfirst($request->scope))) {
                $leads->{'has' . ucfirst($request->scope)}($scopeId);
            }

            $leads = $leads->with([
                'reps.office',
                'salesPacket',
                'reps.team',
                'appointments' => function ($q) {
                    $q->where('type_id', 6);
                    $q->whereNull('deleted_at');
                }
            ])->get()->sortBy(function ($lead, $key) use ($field) {
                return $lead['appointments'][0][$field];
            });

            $users = [];
            $totals = [];
            $i = 0;
            $groupingType = $request->grouping;
            $leadAppointmentsDates = [];
            $repId = [];
            foreach ($leads as $lead) {
                if ($request->by === 'createdAt') {
                    $leadDate = $lead->appointments[0]->created_at;
                } else {
                    $leadDate = $lead->appointments[0]->start_time;
                }
                if ($groupingType === 'W') {
                    $grouping = Carbon::parse($leadDate)->subHours(7)->next('sunday')->toDateString();
                }
                if ($groupingType === 'd') {
                    $grouping = Carbon::parse($leadDate)->subHours(7)->toDateString();
                }
                if ($groupingType === 'M') {
                    $grouping = Carbon::parse($leadDate)->endOfMonth()->toDateString();
                }
                $leadAppointmentsDates[] = $grouping;
                foreach ($lead->reps as $rep) {
                    if ($rep->pivot->position_id < 4) {
                        $repId[] = $rep;
                    }
                }
            }
            $repId = array_unique($repId);
            $leadAppointmentsDates = array_unique($leadAppointmentsDates);
            foreach ($leadAppointmentsDates as $date) {
                if ($groupingType === 'W') {
                    $display = Carbon::parse($date)->format('n/j');
                }
                if ($groupingType === 'd') {
                    $display = Carbon::parse($date)->format('n/j');

                }
                if ($groupingType === 'M') {
                    $display = Carbon::parse($date)->format('M');
                }
                foreach ($repId as $rep) {
                    if (!isset($users[$rep->id]['info'])) {
                        $users[$rep->id]['info']['id'] = $rep->id;
                        $users[$rep->id]['info']['name']['first'] = $rep->first_name;
                        $users[$rep->id]['info']['name']['last'] = $rep->first_name;
                        $users[$rep->id]['info']['name']['full'] = $rep->fullName;
                        $users[$rep->id]['info']['terminated'] = (bool)$rep->terminated;
                        $users[$rep->id]['info']['team_id'] = $rep->team_id;
                        $users[$rep->id]['total']['sat'] = 0;
                        $users[$rep->id]['total']['appointments'] = 0;
                        $users[$rep->id]['total']['closed'] = 0;
                        $users[$rep->id]['total']['installs'] = 0;
                        $users[$rep->id]['total']['creditPass'] = 0;
                    }
//                    dump($date);
                    $users[$rep->id]['datesArray'][$date]['id'] = $rep->id;
                    $users[$rep->id]['datesArray'][$date]['sat'] = 0;
                    $users[$rep->id]['datesArray'][$date]['appointments'] = 0;
                    $users[$rep->id]['datesArray'][$date]['closed'] = 0;
                    $users[$rep->id]['datesArray'][$date]['installs'] = 0;
                    $users[$rep->id]['datesArray'][$date]['creditPass'] = 0;
                    $users[$rep->id]['datesArray'][$date]['leadIds'] = [];
                    $users[$rep->id]['datesArray'][$date]['sort'] = count($users[$rep->id]);
                    $users[$rep->id]['datesArray'][$date]['display'] = $display;

                }
            }
            foreach ($leads as $key => $lead) {

                if ($request->scope === 'user') {
                    $count = $lead->reps->where('pivot.position_id', '<', 4)->where('id', $request->userId)->count();
                    if ($count < 1) {
                        continue;
                    }
                }
                if ($request->by === 'createdAt') {
                    $leadDate = $lead->appointments[0]->created_at;
                } else {
                    $leadDate = $lead->appointments[0]->start_time;
                }
                if ($groupingType === 'W') {
                    $grouping = Carbon::parse($leadDate)->subHours(7)->next('sunday')->toDateString();
                    $display = Carbon::parse($leadDate)->next('sunday')->format('n/j');
                }
                if ($groupingType === 'd') {
                    $grouping = Carbon::parse($leadDate)->subHours(7)->toDateString();
                    $display = Carbon::parse($leadDate)->format('n/j');

                }
                if ($groupingType === 'M') {
                    $grouping = Carbon::parse($leadDate)->endOfMonth()->subHours(7)->toDateString();
                    $display = Carbon::parse($leadDate)->format('M');
                }
                if (!isset($totals[$grouping]['appointments'])) {

                    $totals[$grouping]['appointments'] = 1;
                    $totals[$grouping]['display'] = $display;
                    $totals[$grouping]['sort'] = $i;
                    $i++;
                } else {
                    $totals[$grouping]['appointments']++;
                }

                if ($lead->credit_status_id == 2) {
                    if (!isset($totals[$grouping]['creditPass'])) {
                        $totals[$grouping]['creditPass'] = 1;
                    } else {
                        $totals[$grouping]['creditPass']++;
                    }
                } else {
                    if (!isset($totals[$grouping]['creditPass'])) {
                        $totals[$grouping]['creditPass'] = 0;
                    }
                }
                if ($lead->salesPacket->sat) {
                    if (!isset($totals[$grouping]['sat'])) {
                        $totals[$grouping]['sat'] = 1;
                    } else {
                        $totals[$grouping]['sat']++;
                    }
                } else {
                    if (!isset($totals[$grouping]['sat'])) {
                        $totals[$grouping]['sat'] = 0;
                    }
                }
                if ($lead->close_date !== null) {
                    if (!isset($totals[$grouping]['closed']['value'])) {
                        $totals[$grouping]['closed']['value'] = 1;
                        $totals[$grouping]['closed']['leadId'][] = $lead->id;
                    } else {
                        $totals[$grouping]['closed']['value']++;
                        $totals[$grouping]['closed']['leadId'][] = $lead->id;
                    }
                } else {
                    if (!isset($totals[$grouping]['closed'])) {
                        $totals[$grouping]['closed']['value'] = 0;
                    }
                }
                if ($lead->status_id === 12 || $lead->status_id === 13) {

                    if (!isset($totals[$grouping]['installs'])) {
                        $totals[$grouping]['installs'] = 1;
                    } else {
                        $totals[$grouping]['installs']++;
                    }
                } else {
                    if (!isset($totals[$grouping]['installs'])) {
                        $totals[$grouping]['installs'] = 0;
                    }
                }
                foreach ($lead->reps as $rep) {

                    if ($rep->pivot->position_id === 1) {
                        $this->assignArrayByMonth($rep, $users, $lead, $grouping);
                    }
                    if ($rep->pivot->position_id === 2) {
                        $this->assignArrayByMonth($rep, $users, $lead, $grouping);
                    }
                    if ($rep->pivot->position_id === 3) {
                        $this->assignArrayByMonth($rep, $users, $lead, $grouping);
                    }
                }
            }
            return ['totals' => $totals, 'report' => $users];
        });
        $payload['draw'] = $request->draw;
        return $payload;

    }
    function between($start, $end)
    {
        $start = Carbon::createFromDate($start)->startOfDay()->toDateTimeString();
        $end = Carbon::createFromDate($end)->endOfDay()->toDateTimeString();
        return [Carbon::parse($start . ' America/Los_Angeles')->tz('UTC'),
            Carbon::parse($end . ' America/Los_Angeles')->tz('UTC')];
    }
}
