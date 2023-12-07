<?php


namespace App\Http\Controllers\Api\SalesFlow\Reporting;


use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\Office\Team;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadStatus;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Models\SalesFlow\Position\Position;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Type;
use function Aws\map;

class ReportController extends Controller
{

    public function creditPassWithAppointment(Request $request)
    {
        $to = Carbon::now()->toDateTimeString();
        $from = Carbon::now()->subdays(120)->toDateTimeString();

        if ($request->pickerRangeValues) {
            $temp = json_decode($request->pickerRangeValues, true);
            if ($temp['end'] === null){
                return null;
            }

            $from = Carbon::createFromFormat('Y-m-d', $temp['start'], 'America/Los_Angeles')->startOfDay()->setTimezone('UTC');
            $to = Carbon::createFromFormat('Y-m-d', $temp['end'], 'America/Los_Angeles')->endOfDay()->setTimezone('UTC');
        }

        $leadByAppointment = Lead::query();

        if ($request->user_id) {
            $leadByAppointment->hasUser($request->user_id);
        } elseif ($request->office_id) {
            $leadByAppointment->hasOffice($request->office_id);
        } elseif ($request->region_id) {
            $leadByAppointment->hasRegion($request->region_id);
        }


        $leadByAppointment->whereHas('appointments', function ($q) use ($from, $to) {
            $q->where('type_id', 6)->whereNull('deleted_at')->whereBetween('created_at', [$from, $to]);
        });

        $leadByAppointments = $leadByAppointment->with('appointments')->get();

        $creditPassesWithAppointment = 0;

        foreach ($leadByAppointments as $lead) {
            foreach ($lead->appointments as $appointment) {
                if ($lead->credit_status_id == 2 && $appointment->type_id == 6 && $appointment->deleted_at === null) {
                    $creditPassesWithAppointment++;
                }
            }
        }


        $closedDeals = Lead::query();

        if ($request->user_id) {
            $closedDeals->hasUser($request->user_id);
        } elseif ($request->office_id) {
            $closedDeals->hasOffice($request->office_id);
        } elseif ($request->region_id) {
            $closedDeals->hasRegion($request->region_id);
        }

        $closedDeals->whereHas('salesPacket', function ($query)use($from, $to){
            $query->whereBetween('cpuc_doc_signed', [$from, $to]);
        });
        $closedDealsCount = $closedDeals->count();

        $payload = [
            'closesScheduled' => $leadByAppointment->count(),
            'creditPassesWithAppointment' => $creditPassesWithAppointment,
            'closed' => $closedDealsCount,
            'between' => [$from->toDateTimeString(), $to->toDateTimeString()]
        ];

        return $payload;
    }
    public function index()
    {

        return view('backend.Report.vue');
    }

    public function jij(Request $request)
    {

    }

    public function sitRatioOffice(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $sat = $this->getSatLeads($request, $dateRange);

        if ($request->closed) {
            $groupObjective = $this->getGroupObjectiveClosed($sat);
        } else {
            $groupObjective = $this->getGroupObjectiveCreditPass($sat);
        }

        $groupedSat = $sat->groupBy(function ($data) {
            return $data->reps[0]->id;
        });
        $groupedSat->toArray();

        $closedArray = $this->getClosedArray($groupObjective);
        $satArray = $this->getSatArray($groupedSat);

        $rawValue = $this->calculatePercentage($satArray, $closedArray);
        $collections = collect($rawValue)->sortByDesc('percent');

        $chartValues = [];
        $labels = [];
        foreach ($collections as $userName => $percent) {
            array_push($chartValues, $percent['percent']);
            array_push($labels, $userName);
        }

        $payload = [
            'chartValues' => $chartValues,
            'backgroundColor' => $this->getBackgroundColors(),
            'borderColor' => $this->getBorderColors(),
            'labels' => $labels,
            'borderWidth' => 1,
        ];

        $payload = collect($payload);
        return $payload;
    }

    private function getDateRange($request)
    {
        if (!$request->pickerRangeValues) {
            $to = Carbon::now()->toDateTimeString();
            $from = Carbon::now()->subdays(120)->toDateTimeString();
        } else {
            $temp = json_decode($request->pickerRangeValues, true);
            $from = Carbon::createFromDate($temp['start'])->startOfDay()->toDateTimeString();
            $to = Carbon::createFromDate($temp['end'])->endOfDay()->toDateTimeString();
        }

        return [$from, $to];
    }

    private function getSatLeads($request, $dateRange)
    {
        $sat = Lead::query();
        $sat->isSat(true, $dateRange);

        if ($request->office_id) {
            $sat->hasOffice($request->office_id);
        } elseif ($request->region_id) {
            $sat->hasRegion($request->region_id);
        } else {
            $sat->hasUser($request->user_id);
        }

        $sat->whereHas('reps', function ($q) {
            $q->where('position_id', 3);
            $q->where('users.terminated', null);
            $q->where('user_has_leads.deleted_at', null);
        })->with(['reps' => function ($q) {
            $q->where(function ($query) {
                $query->where('position_id', 3)
                    ->orWhere('position_id', 2);
            });
            $q->where('users.terminated', null);
            $q->where('user_has_leads.deleted_at', null);
        }, 'salesPacket', 'customer', 'statusName']);

        return $sat->get();
    }

    private function getGroupObjectiveClosed($sat)
    {
        return $sat->filter(function ($value, $key) {
            return in_array($value->status_id, [6, 7, 8, 9, 10, 11, 12, 13]);
        })->groupBy(function ($data) {
            return $data->reps[0]->id;
        });
    }

    private function getGroupObjectiveCreditPass($sat)
    {
        return $sat->filter(function ($value, $key) {
            return in_array($value->credit_status_id, [2, 5, 6]);
        })->groupBy(function ($data) {
            return $data->reps[0]->id;
        });
    }

    private function getClosedArray($groupObjective)
    {
        $closedArray = [];
        $tempUserId = 0;
        $i = 0;
        foreach ($groupObjective as $userId => $closed) {
            foreach ($closed as $c) {
                if ($userId != $tempUserId) {
                    $i = 1;
                }
                $closedArray[$c->reps[0]->first_name . ' ' . $c->reps[0]->last_name] = $i;
                $tempUserId = $userId;
                $i++;
            }
        }

        return $closedArray;
    }

    private function getSatArray($groupedSat)
    {
        $satArray = [];
        $tempUserId = 0;
        $i = 0;
        foreach ($groupedSat as $userId => $closed) {
            foreach ($closed as $c) {
                if ($userId != $tempUserId) {
                    $i = 1;
                }
                $satArray[$c->reps[0]->first_name . ' ' . $c->reps[0]->last_name] = $i;
                $tempUserId = $userId;
                $i++;
            }
        }

        return $satArray;
    }

    private function calculatePercentage($satArray, $closedArray)
    {
        $rawValue = [];
        foreach ($satArray as $userId => $sat) {
            if (isset($closedArray[$userId])) {
                $percent = round($closedArray[$userId] / $sat * 100, 2);
            } else {
                $percent = 0;
            }
            $rawValue[$userId]['percent'] = $percent;
        }

        return $rawValue;
    }

    private function getBackgroundColors()
    {
        return [
            'rgb(255, 215, 0, 1)',
            'rgb(192, 192, 192, 1)',
            'rgb(140, 120, 83, 1)',
            'rgba(173, 216, 230, 1)',
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ];
    }

    private function getBorderColors()
    {
        return [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ];
    }


    public function closedRatio(Request $request)
    {


//        $statuses = LeadStatus::all();
//        $labels = [];
//        $pieValues = [];
//        $i = 0;
//        $total = 0;


        //        return $request;
        $user = \Auth::user();
        if (!$request->dateRange) {
            $to = Carbon::now()->toDateString();
            $from = Carbon::now()->subdays(30)->toDateString();
        } else {

            $temp = json_decode($request->dateRange, true);
            $from = Carbon::createFromDate($temp['start'])->startOfDay()->toDateString();
            $to = Carbon::createFromDate($temp['end'])->endOfDay()->toDateString();
        }
        $from = Carbon::parse($from . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $to = Carbon::parse($to . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
//        $status = $request->status;
        $userId = $request->user_id;
        $dateRangeArray = [$from, $to];

        $closed = Lead::query();
        $closed->where(function ($query) use ($dateRangeArray) {
            $query->whereHas('salesPacket', function ($q) use ($dateRangeArray) {
                $q->whereBetween('cpuc_doc_signed', $dateRangeArray);
            });
//            $query->orWhereHas('appointments', function ($q) use ($dateRangeArray) {
//                $q->whereBetween('start_time', $dateRangeArray)->where('type_id', 5);
//            });
        });

        if ($request->user_id) {
            $closed->hasUser($request->user_id);
        } elseif ($request->office_id) {
            $closed->hasOffice($request->office_id);
        } elseif ($request->region_id) {
            $closed->hasRegion($request->region_id);
        }



        $closes = $closed->with('appointments', 'salesPacket', 'customer')->get();

        $installed = $closes->filter(function ($value, $key) {
            return in_array($value->status_id, [12, 13]);
        });

        $jij = $closes->filter(function ($value, $key) {
            return in_array($value->status_id, [16, 17, 20, 19]);
        });
        $canceled = $closes->filter(function ($value, $key) {
            return $value->status_id == 21;
        });
        $changeOrder = $closes->filter(function ($value, $key) {
            return $value->status_id == 9;
        });

        $pending = $closes->filter(function ($value, $key) {
            if (!in_array($value->status_id, [16, 17, 20, 19]) && !in_array($value->status_id, [12, 13]) && $value->status_id !== 21) {
                return true;
            }
        });

//        $pendings = $pending->all();
///
        $changeOrderList = array();
        foreach ($changeOrder as $ch) {
            array_push($changeOrderList, ['leadId' => $ch->id, 'customerName' => $ch->customer->fullName]);
        }

        $installedList = array();
        foreach ($installed as $in) {
            array_push($installedList, ['leadId' => $in->id, 'customerName' => $in->customer->fullName]);
        }

        $jijList = array();
        foreach ($jij as $jijs) {
            array_push($jijList, ['leadId' => $jijs->id, 'customerName' => $jijs->customer->fullName]);
        }

        $canceledList = array();
        foreach ($canceled as $can) {
            array_push($canceledList, ['leadId' => $can->id, 'customerName' => $can->customer->fullName]);
        }

        $pendingList = array();
        foreach ($pending as $pen) {
            array_push($pendingList, ['leadId' => $pen->id, 'customerName' => $pen->customer->fullName]);
        }

///
        $installCount = $installed->count();
        $jijCount = $jij->count();
        $closesCount = $closed->count();
        $canceledCount = $canceled->count();

        $pending = $closesCount - ($installCount + $jijCount + $canceledCount + $changeOrder->count());

        $pieValues = [$installCount, $pending, $jijCount, $canceledCount, $changeOrder->count()];
        $payload = [
            'details' => [
                'installed' => $installedList,
                'jij' => $jijList,
                'canceled' => $canceledList,
                'pending' => $pendingList
            ],
            'datasets' => [
                'data' => $pieValues,
                'backgroundColor' => [
                    'green',
                    'purple',
                    'indigo',
                    'red',
                    'yellow'
                ],
            ],
            'labels' => ['Installed', 'Pending', 'Job In Jeopardy', 'Canceled', 'Change Order'],
            'total' => $closesCount,
        ];
        return Collect($payload);
    }

    function regionalFilterCache($regionalOfficeID)
    {
        $key = 'regionalFilterCache.' . $regionalOfficeID;
        return Cache::remember($key, 600, function () use ($regionalOfficeID) {
            $marketId = Office::where('id', $regionalOfficeID)->get()->pluck('market_id');
            return Office::where('market_id', $marketId)->get()->pluck('id')->toarray();
        });


    }

    public function allJobsPie(Request $request)
    {
//        return $request;
        $user = \Auth::user();

        if (!$request->timeRangeValues) {
            $to = Carbon::now()->toDateTimeString();
            $from = Carbon::now()->subdays(30)->toDateTimeString();
        } else {
            $temp = json_decode($request->timeRangeValues, true);
            $from = Carbon::createFromDate($temp['start'])->startOfDay()->toDateTimeString();
            $to = Carbon::createFromDate($temp['end'])->endOfDay()->toDateTimeString();
        }
        if ($request->status) {
            $status = $request->status;
        } else {
            $status = [1, 2];
        }
        $userId = 14;
        if ($request->type === 'office') {
            $preData = Lead::hasOffice($request->office_id)
                ->whereIn('status_id', $status)
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                    $query->orWhereBetween('close_date', [$from, $to]);
                })
                ->select('status_id', DB::raw('count(*) as total'))
                ->groupBy('status_id')
                ->orderBy('total', 'desc')
                ->get();
        } elseif ($request->type === 'rep') {
            $preData = Lead::hasUser($request->user_id)
                ->whereIn('status_id', $status)
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                    $query->orWhereBetween('close_date', [$from, $to]);
                })
                ->select('status_id', DB::raw('count(*) as total'))
                ->groupBy('status_id')
                ->orderBy('total', 'desc')
                ->get();

        } else {
            $preData = Lead::whereIn('status_id', $status)
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                    $query->orWhereBetween('close_date', [$from, $to]);
                })
                ->select('status_id', DB::raw('count(*) as total'))
                ->groupBy('status_id')
                ->orderBy('total', 'desc')
                ->get();
        }
        $statuses = LeadStatus::all();
        $labels = [];
        $pieValues = [];
        $i = 0;
        $total = 0;
        foreach ($preData as $data) {

            array_push($labels, $statuses->where('id', $data->status_id)->first()->name);
            array_push($pieValues, $data->total);
            $total += $data->total;
            $i++;
        }
        $payload = [
            'datasets' => [
                'data' => $pieValues,
                'backgroundColor' => [
                    'green',
                    'red',
                    'blue',
                    'brown',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'


                ],
            ],
            'labels' => $labels,
            'total' => $total,
        ];
        $payload = Collect($payload);
        return $payload;
    }



    public function closeRatio(Request $request)
    {
        $user = \Auth::user();
        $between = $this->getDateTimeRanges($request);
        $source = $request['params']['source'];
        $userId = $request['params']['user_id'];
        $type = $request['params']['type'];

        $closed = $this->getClosedLeads($request, $user, $between, $source, $userId, $type);
        $qualified = $this->getQualifiedLeads($request, $user, $between, $source, $userId, $type);
        $lost = $this->getLostLeads($request, $user, $between, $source, $userId, $type);

        $qualified = $qualified->reject(function ($item) use ($closed) {
            return $closed->contains('id', $item->id);
        });

        $closedAndQual = $qualified->filter(function ($item) {
          return $item->close_date != null;
        });

        $closed = $closed->merge($closedAndQual);

        $payload = $this->createCloseRatioPayload($closed, $qualified, $lost);
        return Collect($payload);
    }

    private function getDateTimeRanges($request)
    {


        $from = Carbon::createFromDate($request['params']['dateRange']['start'])->startOfDay()->toDateTimeString();
        $to = Carbon::createFromDate($request['params']['dateRange']['end'])->endOfDay()->toDateTimeString();
        $from = Carbon::parse($from . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $to = Carbon::parse($to . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();

        return [$from, $to];
    }

    private function getClosedLeads($request, $user, $between, $source, $userId, $type)
    {
        // Add the logic from your previous code to retrieve closed leads.
        $closed = Lead::query();
        $this->applyFilters($closed, $request, $user, $userId);
        $this->applySourceFilter($closed, $source);
        $closedData = $closed->isClosedDates($between[0], $between[1])
            ->with('customer')
            ->get();

        return $closedData;
    }

    private function applyFilters($query, $request, $user, $userId)
    {


        if ($userId) {
            $query->whereHas('reps', function ($q) use ($userId) {
                $q->where('user_id', $userId);
                $q->where('user_has_leads.deleted_at', null);
            });
        } else if ($request['params']['office_id']) {
            $query->hasOffice($request['params']['office_id']);
        } elseif ($request->region_id) {
            $query->hasRegion($request['params']['region_id']);
        }

        if ($user->hasAnyRole(['executive', 'administrator', 'proposal builder', 'pre sale', 'account manager'])) {

        } else if ($user->hasRole('regional manager')) {
            $officeArray = $this->regionalFilterCache($user->office_id);
            $query->whereIn('office_id', $officeArray);
        } else if ($user->hasRole('manager')) {
            $query->hasOffice($user->office_id);
        } else {
            $query->hasUser($request->user_id);
        }

        return $this->applySourceFilter($query, $request->source);
    }

    private function applySourceFilter($query, $source)
    {
        if ($source === 'call-center') {
            $query->where('source', 'call center');
        } else if ($source === 'self-gen') {
            $query->where('source', 'self gen');
        } else if ($source === 'canvasser') {
            $query->where('source', 'canvasser');
        }

        return $query;
    }

    private function getQualifiedLeads($request, $user, $between, $source, $userId, $type)
    {
        $qualified = Lead::query();
        $qualified = $this->applyFilters($qualified, $request, $user, $userId);
        $qualified = $this->applySourceFilter($qualified, $source);

        if ($type === 'credit-pass-to-close') {
            $qualified->isCreditPass(true)
                ->whereIn('status_id', [3, 4, 5]);
            $qualified->hasCloseRange($between);
        } else if ($type === 'appointments-to-close') {
            $qualified->hasCloseRange($between);
        } else {
            $qualified->whereHas('salesPacket', function ($q) {
                $q->where('sat', 1);
            });
        }

        $qualified->hasCloseRange($between);
        $qualifiedData = $qualified->with('customer')->get();
        return $qualifiedData;
    }

    private function getLostLeads($request, $user, $between, $source, $userId, $type)
    {
        $lost = Lead::query();
        $this->applyFilters($lost, $request, $user, $userId);
        $this->applySourceFilter($lost, $source);

        if ($type === 'credit-pass-to-close') {
            $lost->isCreditPass(true);
        } else if ($type === 'appointments-to-close') {
            $lost->hasCloseRange($between);
        } else {
            $lost->whereHas('salesPacket', function ($q) {
                $q->where('sat', 1);
            });

        }
        $lost->hasCloseRange($between);
        $lost->isLostOpportunity();

        $lostData = $lost->get();
        return $lostData;
    }

    private function createCloseRatioPayload($closed, $qualified, $lost)
    {
        $labels = [];
        $pieValues = [];
        $total = 0;

        if ($closed->count()) {
            array_push($labels, 'Closed');
            array_push($pieValues, $closed->count());
        }

        if ($qualified->count()) {
            array_push($labels, 'Qualified');
            array_push($pieValues, $qualified->count());
        }

        if ($lost->count()) {
            array_push($labels, 'Lost');
            array_push($pieValues, $lost->count());
        }

        $closeList = $closed->map(function ($cd) use (&$total) {
            $total++;
            return ['leadId' => $cd->id, 'customerName' => $cd->customer->fullName];
        });

        $qualifiedList = $qualified->map(function ($qd) use (&$total) {
            $total++;
            return ['leadId' => $qd->id, 'customerName' => $qd->customer->fullName];
        });

        $lostList = $lost->map(function ($ld) use (&$total) {
            $total++;
            return ['leadId' => $ld->id, 'customerName' => $ld->customer->fullName];
        });

        $payload = [
            'details' => [
                'closed' => $closeList,
                'qualified' => $qualifiedList,
                'lost' => $lostList
            ],
            'datasets' => [
                'data' => $pieValues,
                'backgroundColor' => [
                    'green',
                    'blue',
                    'red',
                ],
            ],
            'labels' => $labels,
            'total' => $total,
        ];

        return Collect($payload);
    }


    public function startToClose(Request $request)
    {

        if (!$request->pickerRangeValues) {
            $to = Carbon::now()->toDateTimeString();
            $start = Carbon::now()->subdays(30)->toDateTimeString();
        } else {
            $temp = json_decode($request->pickerRangeValues, true);

            $start = Carbon::createFromDate($temp['start'])->startOfDay()->toDateTimeString();
            $to = Carbon::createFromDate($temp['end'])->endOfDay()->toDateTimeString();
        }
        $start = Carbon::parse($start . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $to = Carbon::parse($to . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $dateRangeArray = [$start, $to];


        $preData = Lead::query();
        $preData->isinstalled();
        $position = $request->position;
        $preData->whereHas('reps', function ($q) use ($position) {
            $q->where('position_id', $position);
            $q->where('users.terminated', null);
            $q->where('user_has_leads.deleted_at', null);
        })->whereHas('appointments', function ($q) use ($dateRangeArray) {
            $q->where('type_id', 5);
            $q->whereBetween('start_time', $dateRangeArray);
        })->with(['reps' => function ($q) use ($position) {
            $q->where('position_id', $position);
        }, 'salesPacket',
            'appointments' => function ($q) {
                $q->where('type_id', 5);
                $q->orWhere('type_id', 4);
            }]);


        if ($request->office_id > 0) {

            $preData->hasOffice($request->office_id);
        } elseif ($request->region_id) {

            $preData->hasRegion($request->region_id);
        }

//        if ($officeId == null) {
//            return null;
//        } else {
//
//            $preData->hasOffice($officeId);
//        }


        $fetchedData = $preData->get();
        $userName = User::all();
        $parseArray = [];
        $diffTotal = 0;
        $i = 0;
        foreach ($fetchedData as $data) {
            $createdAt = null;
            $repName = null;
            $installDate = null;
            $siteSurveyDate = null;
            $closeDate = null;
            if (!isset($data->reps[0]) || !isset($data->appointments[0])) {
                continue;
            }
            $createdAt = Carbon::parse($data->created_at);
            $repName = $data->reps[0]->full_name;
            $siteSurveyRaw = $data->appointments->where('type_id', 4)->first();
            $installDateRaw = $data->appointments->where('type_id', 5)->first();
            if ($installDateRaw === null || $siteSurveyRaw === null) {
                continue;
            }
            $siteSurveyDate = Carbon::parse($siteSurveyRaw->start_time);
            $installDate = Carbon::parse($installDateRaw->start_time);

            $closeDate = Carbon::parse($data->salesPacket->cpuc_doc_signed);

            if ($request->type === 'close') {
                $diff = $createdAt->diffInDays($closeDate);
//
            } else if ($request->type === 'install') {

                $diff = $closeDate->diffInDays($installDate);

            } else if ($request->type === 'all') {
                $diff = $createdAt->diffInDays($installDate);
            } else if ($request->type === 'site-survey') {
                $diff = $siteSurveyDate->diffInDays($installDate);
            }
            $diffTotal += $diff;

            $userId = (int)$data->reps[0]->id;
            if (isset($parseArray[$userId])) {
                $parseArray[$userId]['time'][$data->id] = $diff;
                $parseArray[$userId]['total'] += $diff;

            } else {
                $parseArray[$userId]['time'][$data->id] = $diff;
                $parseArray[$userId]['total'] = $diff;
            }
            $parseArray[$userId]['name'] = $repName;

            $i++;
        }

        $secondArray = [];
        $sortArray = [];

        foreach ($parseArray as $parse) {
            $average = $parse['total'] / count($parse['time']);
            array_push($secondArray, round($average));
            array_push($sortArray, $parse);
        }

        arsort($secondArray, 1);
        $payloadArray = [];

        foreach ($secondArray as $order => $v) {
            $payloadArray[$order]['average'] = $v;
            $payloadArray[$order]['name'] = $sortArray[$order]['name'];
            $i++;
        }

        if ($i == 0) {
            return null;
        }
        $payloadArray = array_reverse($payloadArray);
        $labels = [];
        $chartValues = [];
        foreach ($payloadArray as $parse) {
            array_push($labels, $parse['name']);
            array_push($chartValues, round($parse['average'], 2));
        }

        $totalAverage = $diffTotal / $i;

        array_push($labels, 'Average');
        array_push($chartValues, round($totalAverage, 2));

        //
        $payload = [
            'datasets' => [

                'data' => $chartValues,
                'backgroundColor' => [
                    'rgb(255, 215, 0, 1)',
                    'rgb(192, 192, 192, 1)',
                    'rgb(140, 120, 83, 1)',
                    'rgba(173, 216, 230, 1)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                'borderColor' => [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
            ],
            'details' => $parseArray,

            'borderWidth' => 1,
            'labels' => $labels,

        ];


        $payload = Collect($payload);
        return $payload;
    }

    public function sitRatio(Request $request)
    {
        $user = \Auth::user();
        $between = $this->getDateTimeRange($request);
        $sat = $this->getLeads($request, $between, $user);
        $satCount = $this->countSits($sat, true);
        $didntSitCount = $this->countSits($sat, false);

        $payload = $this->createPayload($satCount, $didntSitCount);

        return Collect($payload);
    }

    private function getDateTimeRange(Request $request)
    {

        $from = Carbon::createFromDate($request->params['timeRangeValues']['start'])->startOfDay()->toDateTimeString();
        $to = Carbon::createFromDate($request->params['timeRangeValues']['end'])->endOfDay()->toDateTimeString();

        $from = Carbon::parse($from . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $to = Carbon::parse($to . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();

        return [$from, $to];
    }

    private function getLeads(Request $request, array $between, $user)
    {
        $leadsQuery = Lead::query();
        $leadsQuery->hasCloseRange($between);

        if ($request['params']['user_id']) {
            $leadsQuery->hasUser($request['params']['user_id']);
        } elseif ($request['params']['office_id']) {
            $leadsQuery->hasOffice($request['params']['office_id']);
        } elseif ($request['params']['region_id']) {
            $leadsQuery->hasRegion($request['params']['region_id']);
        }

        $this->applyUserFilters($leadsQuery, $user, $request);

        return $leadsQuery->with('salesPacket')->get();
    }

    private function applyUserFilters($leadsQuery, $user, Request $request)
    {
        if ($user->hasAnyRole(['executive', 'administrator', 'proposal builder', 'pre sale', 'account manager']) && !$request->user_id) {
            return;
        } elseif ($user->hasRole('regional manager')) {

            $officeArray = $this->regionalFilterCache($user->office_id);
            $leadsQuery->whereIn('office_id', $officeArray);
        } elseif ($user->hasRole('manager')) {

            $leadsQuery->hasOffice($user->office_id);
        } else {

            $leadsQuery->hasUser($request->user_id);
        }
    }

    private function countSits($leads, $sat)
    {
        return $leads->filter(function ($value, $key) use ($sat) {
            return $value->salesPacket->sat == $sat;
        })->count();
    }

    private function createPayload(int $satCount, int $didntSitCount)
    {
        $labels = [];
        $pieValues = [];
        $total = 0;

        if ($satCount) {
            array_push($labels, 'Sat');
            array_push($pieValues, $satCount);
            $total += $satCount;
        }

        if ($didntSitCount) {
            array_push($labels, 'Did Not Sit');
            array_push($pieValues, $didntSitCount);
            $total += $didntSitCount;
        }

        return [
            'datasets' => [
                'data' => $pieValues,
                'backgroundColor' => [
                    'green',
                    'red',
                ],
            ],
            'labels' => $labels,
            'total' => $total,
        ];
    }

    public function sitRatioDetailed(Request $request)
    {
        $user = \Auth::user();
        $timeRange = $this->getDateTimeRange($request);
        $officeId = $request['params']['office_id'];
        $userId = $request['params']['user_id'];
        $regionId = $request['params']['region_id'];
        $satLeads = $this->getSatLeadsDetails($officeId, $userId, $regionId, $timeRange);
        $didntSitLeads = $this->getDidntSitLeads($officeId, $userId, $regionId, $timeRange);

        $payload = [
            'sat' => $this->mapLeads($satLeads),
            'didntSit' => $this->mapLeads($didntSitLeads),
        ];

        return collect($payload);
    }

    private function getTimeRange(Request $request)
    {
        if (!$request->timeRangeValues) {
            $from = Carbon::now()->subDays(30);
            $to = Carbon::now();
        } else {
            $temp = json_decode($request->timeRangeValues, true);
            $from = Carbon::createFromDate($temp['start'])->startOfDay();
            $to = Carbon::createFromDate($temp['end'])->endOfDay();
        }

        return [
            $from->timezone('America/Los_Angeles')->toDateTimeString(),
            $to->timezone('America/Los_Angeles')->toDateTimeString(),
        ];
    }

    private function getSatLeadsDetails($officeId, $userId, $regionId, $timeRange)
    {
        $query = Lead::query()
            ->isSat(true, $timeRange)
            ->with('customer');

        if ($userId) {
            $query->hasUser($userId);

        } elseif ($officeId) {
            $query->hasOffice($officeId);

        } elseif ($regionId)  {
            $query->hasRegion($regionId);
        }

        return $query->get();
    }

    private function getDidntSitLeads($officeId, $userId, $regionId, $timeRange)
    {
        $query = Lead::query()
            ->isSat(false, $timeRange)
            ->with('customer');

        if ($userId) {
            $query->hasUser($userId);

        } elseif ($officeId) {
            $query->hasOffice($officeId);

        } elseif ($regionId)  {
            $query->hasRegion($regionId);
        }

        return $query->get();
    }

    private function mapLeads($leads)
    {
        return $leads->mapWithKeys(function ($item) {
            return [
                $item['id'] => $item['customer']['first_name'] . ' ' . $item['customer']['last_name'],
            ];
        });
    }


    public function assignedLeads(Request $request)
    {

        if (!$request->pickerRangeValues) {
            $to = Carbon::now()->toDateTimeString();
            $start = Carbon::now()->subdays(30)->toDateTimeString();
        } else {
            $temp = json_decode($request->pickerRangeValues, true);

            $start = Carbon::createFromDate($temp['start'])->startOfDay()->toDateTimeString();
            $to = Carbon::createFromDate($temp['end'])->endOfDay()->toDateTimeString();
        }

        $start = Carbon::parse($start . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $to = Carbon::parse($to . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();

        $officeId = $request->office_id;
        $regionId = $request->regionId;
        $position = $request->position;
        $between = [$start, $to];
        return $this->extraFunction($request->type, $request->source, $request->ratio, $position, $officeId, $regionId, $between);


    }

    function setTypeFilters($preData, $type, $between) {
        $typeFilters = [
            'credit_pass_ratio' => function ($preData) use ($between) {
                $preData->whereBetween('created_at', $between);
                $preData->isCreditPass(true);
            },
            'appointments_ratio' => function ($preData) use ($between) {
                $preData->whereHas('appointments', function ($q) use ($between) {
                    $q->where('type_id', 6);
                    $q->whereBetween('start_time', $between);
                });
            },
            'close' => function ($preData) use ($between) {
                $preData->isClosedDates($between[0], $between[1]);
            },
            'credit_pass' => function ($preData) use ($between) {
                $preData->whereBetween('created_at', $between);
                $preData->isCreditPass(true)->get();
            },
            'install' => function ($preData) use ($between) {
                $preData->isInstalledDates($between[0], $between[1]);
            },
            'appointments' => function ($preData) use ($between) {
                $preData->whereHas('appointments', function ($q) use ($between) {
                    $q->where('type_id', 6);
                    $q->whereBetween('created_at', $between);
                });
            },
        ];

        if (array_key_exists($type, $typeFilters)) {
            $typeFilters[$type]($preData);
        } else {
            $preData->isSat(true, $between);
        }

        return $preData;
    }

    function extraFunction($type, $source, $ratio, $position, $officeId, $regionId, $between)
    {
        $preData = Lead::query();

        $this->setTypeFilters($preData, $type, $between);

        $sourceMap = [
            'call-center' => 'call center',
            'self-gen' => 'self gen',
            'canvasser' => 'canvasser',
        ];

        if (isset($sourceMap[$source])) {
            $preData->where('source', $sourceMap[$source]);
        }


        $repsConditions = [
            ['position_id', '=', $position],
            ['user_has_leads.deleted_at', '=', null],
        ];

        $preData->whereHas('reps', function ($q) use ($repsConditions) {
            $q->where($repsConditions);
        })->with(['reps' => function ($q) use ($repsConditions) {
            $q->where($repsConditions);
        }]);

        if ($officeId) {
            $preData->hasOffice($officeId);
        } elseif ($regionId) {
            $preData->hasRegion($regionId);
        }


        $fetchedData = $preData->with('customer')->get();

        $merged = $fetchedData;
        $userName = User::all();
        $labels = [];
        $pieValues = [];
        $i = 0;
        $total = 0;
        $parseArray = [];
        $detailsArray = [];

        foreach ($merged as $data) {
            if (!$data->customer) {
                continue;
            }
            $userId = (int)$data->reps[0]->id;

            if (key_exists($userId, $parseArray)) {
                $parseArray[$userId]['total']++;
                if ($data->isClosed() && $ratio === 'true') {
                    $parseArray[$userId]['closed']++;
                }
            } else {
                $parseArray[$userId]['total'] = 1;
                if ($data->isClosed() && $ratio === 'true') {
                    $parseArray[$userId]['closed'] = 1;
                } else {
                    $parseArray[$userId]['closed'] = 0;
                }
            }

            $detailsArray[$userId]['leads'][$i]['id'] = $data->id;
            $detailsArray[$userId]['leads'][$i]['name'] = $data->customer->first_name . ' ' . $data->customer->last_name;
            $detailsArray[$userId]['name'] = $data->reps[0]->full_name;
//            }

            $parseArray[$userId]['name'] = $data->reps[0]->full_name;
            $total++;
            $i++;
        }

        foreach ($parseArray as $userId => $value) {
            if ($ratio == 'true') {
                if ($value['total'] == 0) {
                    $parseArray[$userId]['total'] = 0;
                } else {
                    $parseArray[$userId]['total'] = round($value['closed'] / $value['total'] * 100, 2);
                }
            }
        }



        $payloadArray = [];

        foreach ($parseArray as $parse) {
            if (isset($parse['total'])) {
                $payloadArray[] = [
                    'total' => $parse['total'],
                    'name' => $parse['name'],
                ];
            }
        }

        usort($payloadArray, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $labels = [];
        $pieValues = [];

        foreach ($payloadArray as $parse) {
            $labels[] = $parse['name'];
            $pieValues[] = $parse['total'];
        }

        $detailsPayload = collect($detailsArray);
        $payload = [
            'chartValues' => $pieValues,
            'details' => $detailsPayload,
            'labels' => $labels,
            'borderWidth' => 1,
            'total' => $total,
        ];
        return Collect($payload);
    }

    function rawAssignment($type, $position, $officeId, $regionId, $between)
    {
        $preData = Lead::query();
        if ($type === 'close') {
            $preData->isClosedDates($between[0], $between[1]);
        } else if ($type === 'credit_pass') {
            $preData->whereBetween('created_at', $between);
            $preData->isCreditPass(true)->get();
        } else if ($type === 'install') {
            $preData->isInstalledDates($between[0], $between[1]);
        } else if ($type === 'appointment') {

            $preData->whereHas('appointments', function ($q) use ($between) {
                $q->where('type_id', 6);
                $q->whereBetween('start_time', $between);
            });
        } else {
            $preData->whereBetween('created_at', $between);
            $preData->isSat(true, $between);
        }

//        if (($position == 1 || $position == 2) && ($request->office_id != 10 && $request->regionId != 5)) {
//            $preData->where('source', '!=', 'call center');
//        }

        $preData->whereHas('reps', function ($q) use ($position) {
            $q->where('position_id', $position);
            $q->where('users.terminated', null);
            $q->where('user_has_leads.deleted_at', null);
        })->with(['reps' => function ($q) use ($position) {
            $q->where('position_id', $position);
            $q->where('users.terminated', null);
            $q->where('user_has_leads.deleted_at', null);
        }]);
        if ($officeId) {
            $preData->hasOffice($officeId);
        } elseif ($regionId) {
            $preData->hasRegion($regionId);
        } else {
            return 'ests';
        }


        $parseArray = [];
        $fetchedData = $preData->with('customer')->get();
        $userName = User::all();
        $labels = [];
        $pieValues = [];
        $i = 0;
        $total = 0;

        $detailsArray = [];
        foreach ($fetchedData as $data) {
            $userId = (int)$data->reps[0]->id;
            if (isset($parseArray[$userId])) {
                $parseArray[$userId]['total'] = $parseArray[$userId]['total'] + 1;
            } else {
                $parseArray[$userId]['total'] = 1;
            }
//            $detailsArray[$userId] = $data->reps[0]->full_name;
//            if ($data->reps[0]->full_name) {
            $detailsArray[$data->reps[0]->id]['leads'][$i]['id'] = $data->id;
            $detailsArray[$data->reps[0]->id]['leads'][$i]['name'] = $data->customer->first_name . ' ' . $data->customer->last_name;
            $detailsArray[$data->reps[0]->id]['name'] = $data->reps[0]->full_name;
//            }
            $parseArray[$userId]['name'] = $data->reps[0]->full_name;
            $total++;
            $i++;
        }

        $secondArray = [];
        $sortArray = [];
        foreach ($parseArray as $parse) {
            foreach ($parse as $p => $v) {
                if ($p === 'total') {
                    array_push($secondArray, $v);
                    array_push($sortArray, $parse);
                }
            }
        }


        arsort($secondArray, 1);
        $payloadArray = [];

        foreach ($secondArray as $order => $v) {
            $payloadArray[$order]['total'] = $v;
            $payloadArray[$order]['name'] = $sortArray[$order]['name'];
            $i++;
        }

        foreach ($payloadArray as $parse) {
            array_push($labels, $parse['name']);
            array_push($pieValues, $parse['total']);
        }

        $detailsPayload = collect($detailsArray);
        $payload = [
            'chartValues' => $pieValues,
            'details' => $detailsPayload,
            'labels' => $labels,
            'borderWidth' => 1,
            'total' => $total,
        ];
        return Collect($payload);
    }

    public function assignedLeadsDetailed(Request $request)
    {
//        return $request;
        $user = \Auth::user();


        if (!$request->pickerRangeValues) {
            $to = Carbon::now()->toDateTimeString();
            $start = Carbon::now()->subdays(30)->toDateTimeString();
        } else {
            $temp = json_decode($request->pickerRangeValues, true);

            $start = Carbon::createFromDate($temp['start'])->startOfDay()->toDateTimeString();
            $to = Carbon::createFromDate($temp['end'])->endOfDay()->toDateTimeString();
        }
        $start = Carbon::parse($start . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $to = Carbon::parse($to . ' America/Los_Angeles')->tz('UTC')->toDateTimeString();
        $officeId = $request->office_id;

        $position = $request->position;
        $preData = Lead::query();
        if ($request->type !== 'sit') {
            $preData->isClosedDates($start, $to);
        }
        if ($request->type === 'close') {
            $preData->isClosed();
        } else if ($request->type === 'credit_pass') {
            $preData->isCreditPass(true);
        } else if ($request->type === 'install') {
            $preData->isinstalled();
        } else {
            $preData->isSat(true, [$start, $to]);
        }

        $preData->whereHas('reps', function ($q) use ($position) {
            $q->where('position_id', $position);
            $q->where('user_has_leads.deleted_at', null);
        })->with(['reps' => function ($q) use ($position) {
            $q->where('position_id', $position);

        }])->hasOffice($request->office_id);

        $parseArray = [];
        $fetchedData = $preData->get();
        $userName = User::all();
        $labels = [];
        $pieValues = [];
        $i = 0;
        $total = 0;

        foreach ($fetchedData as $data) {
            $userId = (int)$data->reps[0]->id;
            if (isset($parseArray[$userId])) {
                $parseArray[$userId]['total'] = $parseArray[$userId]['total'] + 1;
            } else {

                $parseArray[$userId]['total'] = 1;
            }
            $parseArray[$userId]['name'] = $data->reps[0]->full_name;
            $total++;
            $i++;
        }
        $secondArray = [];
        $sortArray = [];
        foreach ($parseArray as $parse) {
            foreach ($parse as $p => $v) {
                if ($p === 'total') {
                    array_push($secondArray, $v);
                    array_push($sortArray, $parse);
                }
            }
        }


        arsort($secondArray, 1);
        $payloadArray = [];

        foreach ($secondArray as $order => $v) {

            $payloadArray[$order]['total'] = $v;
            $payloadArray[$order]['name'] = $sortArray[$order]['name'];
            $i++;
        }

        foreach ($payloadArray as $parse) {
            array_push($labels, $parse['name']);
            array_push($pieValues, $parse['total']);

        }

        $payload = [
            'datasets' => [

                'data' => $pieValues,
                'backgroundColor' => [
                    'rgb(255, 215, 0, 1)',
                    'rgb(192, 192, 192, 1)',
                    'rgb(140, 120, 83, 1)',
                    'rgba(173, 216, 230, 1)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                'borderColor' => [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
            ],

            'borderWidth' => 1,
            'labels' => $labels,
            'total' => $total,
        ];
        $payload = Collect($payload);
        return $payload;
    }

    public function kpi(Request $request)
    {
        $between = $this->between($request->dateRange['start'], $request->dateRange['end']);

        $leads = Lead::query();


        if (!\Auth::user()->hasPermissionTo('view company reporting')) {
            $leads->where('office_id', \Auth::user()->office_id);
        }

        $leads = $leads->whereHas('appointments', function ($q) use ($between) {
            $q->where('type_id', 6);
            $q->whereBetween('created_at', $between);
        })->with('reps.office', 'salesPacket')->get();

        $users = [];
        $teams = [];
        $offices = [];

        foreach ($leads as $lead) {
            foreach ($lead->reps as $rep) {
                array_push($teams, $rep->team_id);
                array_push($offices, $rep->office_id);

                if ($rep->pivot->position_id === 1 && $request->positionId === 1) {
                    $this->assignArray($rep, $users, $lead);
                }
                if ($rep->pivot->position_id === 2 && $request->positionId === 2) {
                    $this->assignArray($rep, $users, $lead);
                }
                if ($rep->pivot->position_id === 3 && $request->positionId === 3) {
                    $this->assignArray($rep, $users, $lead);
                }
            }
        }
        $teams = collect($teams);
        $teams = $teams->unique();
        $teams = $teams->values()->all();
        $teams = Team::whereIn('id', $teams)->pluck('name', 'id');

        $teamArray = [];
        $i = 0;
        foreach ($teams as $key => $team) {
            $teamArray[$i]['name'] = $team;
            $teamArray[$i]['id'] = $key;
            $i++;
        }

        $offices = collect($offices);
        $offices = $offices->unique();
        $offices = $offices->values()->all();
        $offices = Office::whereIn('id', $offices)->pluck('name', 'id');
        $officeArray = [];
        $i = 0;
        foreach ($offices as $key => $office) {
            $officeArray[$i]['name'] = $office;
            $officeArray[$i]['id'] = $key;
            $i++;
        }

        $payLoad = [];
        $i = 0;
        foreach ($users as $id => $user) {
            $payLoad[$i] = $user;
            $i++;
        }

        return ['report' => $payLoad, 'teams' => $teamArray, 'offices' => $officeArray];
    }

    function assignArray($user, &$array, $lead)
    {
        if ($user !== null && $user->pivot->position_id < 4) {
            if (isset($array[$user->id])) {
                if (in_array($lead->id, $array[$user->id]['leadIds'])) {
                    if ($user->pivot->position_id > $array[$user->id]['position_id']) {
                        $array[$user->id]['position_id'] = $user->pivot->position_id;
                    }
                    return 0;
                }
                $array[$user->id]['appointments']++;
                if ($lead->salesPacket->sat) {
                    $array[$user->id]['sat']++;
                }
                if ($lead->close_date !== null) {
                    $array[$user->id]['closed']++;
                }
                if ($lead->isCreditPass()) {
                    $array[$user->id]['creditPass']++;
                }
                if ($lead->status_id === 12 || $lead->status_id === 13) {
                    $array[$user->id]['installs']++;
                }
                if ($user->pivot->position_id > $array[$user->id]['position_id']) {
                    $array[$user->id]['position_id'] = $user->pivot->position_id;
                }

                array_push($array[$user->id]['leadIds'], $lead->id);
            } else {
                $array[$user->id]['id'] = $user->id;
                $array[$user->id]['name']['first'] = $user->first_name;
                $array[$user->id]['name']['last'] = $user->first_name;
                $array[$user->id]['name']['full'] = $user->fullName;
                $array[$user->id]['leadIds'][0] = $lead->id;
                $array[$user->id]['appointments'] = 1;
                $array[$user->id]['terminated'] = (bool)$user->terminated;
                $array[$user->id]['team_id'] = $user->team_id;
                $array[$user->id]['office_id'] = $user->office_id;
                $array[$user->id]['position_id'] = $user->pivot->position_id;

                if ($lead->salesPacket->sat) {
                    $array[$user->id]['sat'] = 1;
                } else {
                    $array[$user->id]['sat'] = 0;
                }
                if ($lead->close_date !== null) {
                    $array[$user->id]['closed'] = 1;
                } else {
                    $array[$user->id]['closed'] = 0;
                }
                if ($lead->isCreditPass()) {
                    $array[$user->id]['creditPass'] = 1;
                } else {
                    $array[$user->id]['creditPass'] = 0;
                }
                if ($lead->status_id === 12 || $lead->status_id === 13) {
                    $array[$user->id]['installs'] = 1;
                } else {
                    $array[$user->id]['installs'] = 0;
                }

            }
        }
        return null;
    }

    function assignArrayByMonth($user, &$array, $lead, $grouping)
    {
        if (!in_array($lead->id, $array[$user->id]['datesArray'][$grouping]['leadIds'])) {
            $array[$user->id]['datesArray'][$grouping]['appointments']++;
            $array[$user->id]['total']['appointments']++;
            $array[$user->id]['datesArray'][$grouping]['leadIds'][] = $lead->id;
            $leadInfo = ['id' => $lead->id, 'position_id' => $user->pivot->position_id];
            $array[$user->id]['datesArray'][$grouping]['leads'][] = $leadInfo;
        } else {
            return null;
        }
        if ($lead->salesPacket->sat) {
            $array[$user->id]['datesArray'][$grouping]['sat']++;
            $array[$user->id]['total']['sat']++;
        }
        if ($lead->close_date !== null) {
            $array[$user->id]['datesArray'][$grouping]['closed']++;
            $array[$user->id]['total']['closed']++;
        }
        if ($lead->credit_status_id == 2) {
            $array[$user->id]['datesArray'][$grouping]['creditPass']++;
            $array[$user->id]['total']['creditPass']++;
        }
        if ($lead->status_id === 12 || $lead->status_id === 13) {
            $array[$user->id]['datesArray'][$grouping]['installs']++;
            $array[$user->id]['total']['installs']++;
        }
//            $array[$user->id][$grouping]['leads'][] = $leadInfo;


//            $leadInfo = ['id' => $lead->id, 'position_id' => $user->pivot->position_id];


    }


    function between($start, $end)
    {
        $start = Carbon::createFromDate($start)->startOfDay()->toDateTimeString();
        $end = Carbon::createFromDate($end)->endOfDay()->toDateTimeString();
        return [Carbon::parse($start . ' America/Los_Angeles')->tz('UTC'),
            Carbon::parse($end . ' America/Los_Angeles')->tz('UTC')];
    }

    public function runChartTables(Request $request)
    {
        $typeId = null;
        if ($request->scope === 'region') {
            $typeId = $request->regionId;
        } elseif ($request->scope === 'office') {
            $typeId = $request->officeId;
        } elseif ($request->scope === 'user') {
            $typeId = $request->userId;
        }
        $key = "kpi.run-chart.type.$request->scope.id.$typeId.by.$request->by.grouping.$request->grouping";
        $payload = Cache::remember($key, 43200, function () use ($request) {
            $between = $this->between(Carbon::now()->startOfYear()->timezone(7)->toDateTimeString(), Carbon::now(7));
            $leads = Lead::query();
            if ($request->by === 'createdAt') {
                $leads->whereHas('appointments', function ($q) use ($between) {
                    $q->where('type_id', 6);
                    $q->whereBetween('created_at', $between);
                    $q->where('deleted_at', null);
                });
            }
            if ($request->by === 'startTime') {
                $leads->whereHas('appointments', function ($q) use ($between) {
                    $q->where('type_id', 6);
                    $q->whereBetween('start_time', $between);
                    $q->where('deleted_at', null);
                });
            }
            if ($request->scope === 'region') {
                $leads->hasRegion($request->regionId);
            }
            if ($request->scope === 'office') {
                $leads->hasOffice($request->officeId);
            }
            if ($request->scope === 'user') {
                $leads->hasUser($request->userId);
            }
            $leads = $leads->with(['reps.office', 'salesPacket', 'reps.team', 'appointments' => function ($q) {
                $q->where('type_id', 6);
                $q->where('deleted_at', null);
            }])->get();
            if ($request->by === 'createdAt') {
                $leads = $leads->sortBy(function ($lead, $key) {
                    return $lead['appointments'][0]['created_at'];
                });
            } else {
                $leads = $leads->sortBy(function ($lead, $key) {
                    return $lead['appointments'][0]['start_time'];
                });
            }
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
}
