<?php

namespace App\Http\Controllers\Api\RoundRobin;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Appointment\Availability;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;

class RoundRobinAvailabilityController extends Controller
{

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

    public function cityListForUserRR()
    {


        $officeRoundRobin = RoundRobin::where('office_id', 34)
            ->where('type', 'Call Center Offices')->first();
//dump($officeRoundRobin->list);
        $eligibleOffices = Office::whereIn('id', $officeRoundRobin->list)
            ->get()
            ->pluck('id');
//            dump($eligibleOffices);

        $officeUserIds = RoundRobin::whereIn('office_id', $eligibleOffices)
            ->where('type', 'Call Center Appointments')
            ->get();

        $officeUserIds = $this->reorganizeBykey($officeUserIds, $eligibleOffices->toArray());

        $merged = array_merge(...$officeUserIds);
        $now = Carbon::now()->timezone('America/los_angeles')->toDateTimeString();
        $users = User::whereIn('id', $merged)
            ->with(['availability' => function ($q) use ($now) {
                $q->where('start', '>', $now);
            },
                'appointments' => function ($q) use ($now) {
                    $q->where('start_time', '>', $now);
                    $q->where('type_id', 6)
                        ->with('lead.customer');
                }])->get();


        return $users;

//
    }

    public function index()
    {


//return Carbon::now()->startOfDay()->timezone('america/los_angeles');

//        ['appointment', function ($A){
//            $A->where('start_time', '>', Carbon::now()->startOfDay()->timezone('america/los_angeles')->toDateTimeString());
//        }, 'availability' => function ($B){
//            $B->where('start', '>', Carbon::now()->startOfDay()->timezone('america/los_angeles')->toDateTimeString());
//        } ]

//        $payload = Cache::remember('Coverage', 6000, function () {
        $users = $this->cityListForUserRR();
        $allCityArray = [];
        $array = [];
        foreach ($users as $user) {
            $cityArray = [];
            foreach ($user->tagsWithType('EligibleCity') as $city) {
                array_push($cityArray, $city->name);
                array_push($allCityArray, $city->name);
                $user->cities = $cityArray;
            }
        }
        $listOfAll = $allCityArray;

//
//            $cityArray = array_unique($array);
//return $cityArray;
//            $currentTime = new Carbon('now');
//            $endTime = new Carbon('+ 2 week');
//            $available = $currentTime->addHour(12);
//            $available->minute(00)->second(0);
//            $dayStart = Carbon::createFromTime(3, 30, '00')->toTimeString();
//            $dayEnd = Carbon::createFromTime(22, 00, '00')->toTimeString();
//            $period = new CarbonPeriod($available, '60 minutes', $endTime);
//
//            $e = -1;
//            $i = 0;
//            $cityArray = [];
//
//
//            foreach ($office->tagsWithType('EligibleCity') as $tag) {
//                array_push($cityArray, $tag->name);
//            }
//
//            $i = 0;
//            $slotCollection = array();
//            $userArray = array();
////        return $slotArray;
//            foreach ($slotArray as $city => $slots) {
//                $userList = array();
//                foreach ($users as $user) {
//
//
//                    if (!in_array($city, $cityArray)) {
//                        continue;
//                    }
//
//                    $availability = $user->availability;
//                    $currentAppointments = $user->appointments;
//
//
//                }
//            }
//
//            $userCityListArray = array();
//            $i = 0;
////            return $slotCollection;
//            foreach ($slotCollection as $topKey => $collection) {
//                $i++;
//                if (isset($collection['user'])) {
//                    foreach ($collection['user'] as $key => $user) {
//
//                        $userCityListArray[$key]['cities'][$i] = $topKey;
//                        $leads = Lead::query();
//
//                        $leads->where('created_at', '>', Carbon::now()->subMonths(2));
//                        $userId = $user['id'];
//                        $leads->whereHas('reps', function ($q) use ($userId) {
//                            $q->where('user_id', $userId);
//                            $q->where('user_has_leads.deleted_at', null);
//                        });
//
//                        $leads = $leads->get();
//                        $totalLeadCount = $leads->count();
//                        $leadsNoneCancelled = $leads->filter(function ($value, $key) {
//                            if ($value->status_id != 14 && $value->status_id != 20) {
//                                return true;
//                            }
//                        });
////                $canceled = $closes->filter(function ($value, $key) {
////                    return $value->status_id == 21;
////                });
//                        $closed = $leads->filter(function ($value, $key) {
//                            if ($value->close_date != null) {
//                                return true;
//                            }
//                        });
//
//                        if ($totalLeadCount > 0) {
//                            $validAppointments = $leadsNoneCancelled->count() - $user['appointments'];
//                            if ($validAppointments != 0 || $closed->count() != 0)
//                                $closedPercent = ($closed->count() / $validAppointments) * 100;
//                            $closedPercent = round($closedPercent, 2);
//                            $slotCollection[$topKey]['user'][$key]['closepercent'] = $closedPercent;
//                        }
//                        $userCityListArray[$key]['name'] = $key;
//                        $userCityListArray[$key]['closingPercent'] = $closedPercent;
//                        $userCityListArray[$key]['totalLeads'] = $leadsNoneCancelled->count();
//                        $userCityListArray[$key]['closedLeads'] = $closed->count();
//                        $userCityListArray[$key]['currentAppointments'] = $user['appointments'];
//                        $userCityListArray[$key]['availableAppointments'] = $user['available'];
//                    }
//                }
//            }
//        $slotCollection = collect($listOfAll);
        $usersWithList = $users->map(function ($item, $key) {
            return [
                'id' => $item['id'],
                'name' => $item['full_name'],
                'cities' => $item['cities'],
                'avatar' => $item['picture']
            ];
        });
        $payload = [
            'UserCity' => $usersWithList,
            'ListOfAll' => $listOfAll

        ];
        return collect($payload);
//        });
//        return $payload;

//        return $userCityListArray;
        return collect($slotCollection);


    }

    public function byCities()
    {
        $leads = Lead::where('source', 'call center')->whereHas('office', function ($q) {
            $q->where('market_id', 14);
        })->whereHas('appointments', function ($q){ $q->where('remote', false)->where('type_id', 6);})->with(['appointments' => function ($q) {
            $q->where('type_id', 6);
            $q->where('remote', false);

        },
            'system', 'customer', 'salesPacket'])->get();

        $users = User::whereHas('office', function ($q) {
            $q->where('market_id', 14);
        })->with(['availability' => function ($q) {
            $q->where('type', 'call-center')->where('start', '>', Carbon::now());
        }])->get();
        $cityList = [];
        foreach ($leads as $lead) {
            if (!$lead->customer){
                continue;
            }
            if ($lead->customer->city === '') {
                continue;
            }
            $city = strtolower(trim( $lead->customer->city));
            $cityList[$city]['name'] = $city;
            if ($lead->appointments->count()) {

                if (Carbon::parse($lead->appointments[0]->start_time) < Carbon::now()) {
                    if (isset($cityList[$city]['appointments'])) {
                        $cityList[$city]['appointments']++;
                    } else {
                        $cityList[$city]['appointments'] = 1;
                    }
                }else{
                    if (!isset($cityList[$city]['appointments'])) {
                        $cityList[$city]['appointments'] = 0;
                    }
                }

                if (Carbon::parse($lead->appointments[0]->start_time) > Carbon::now()) {
                    if (isset($cityList[$city]['futureAppointments'])) {
                        $cityList[$city]['futureAppointments']++;
                    } else {
                        $cityList[$city]['futureAppointments'] = 1;
                    }
                }
            }
            if ($lead->system) {
                if (isset($cityList[$city]['closed'])) {
                    $cityList[$city]['closed']++;
                } else {
                    $cityList[$city]['closed'] = 1;
                }
                if (isset($cityList[$city]['kwh'])) {
                    $cityList[$city]['kwh'] += $lead->system->system_size;
                } else {
                    $cityList[$city]['kwh'] = $lead->system->system_size;
                }
            }
            if ($lead->salesPacket->sat) {
                if (isset($cityList[$city]['sat'])) {
                    $cityList[$city]['sat']++;
                } else {
                    $cityList[$city]['sat'] = 1;
                }
            }
            if (!isset($cityList[$city]['closed'])) {
                $cityList[$city]['closed'] = 0;
            }
            if (!isset($cityList[$city]['kwh'])) {
                $cityList[$city]['kwh'] = 0;
            }

            if (!isset($cityList[$city]['appointments'])) {
                $cityList[$city]['appointments'] = 0;
            }
            if (!isset($cityList[$city]['futureAppointments'])) {
                $cityList[$city]['futureAppointments'] = 0;
            }
            if (!isset($cityList[$city]['sat'])) {
                $cityList[$city]['sat'] = 0;
            }
            if (!isset($cityList[$city]['userCount'])) {
                $cityList[$city]['userCount'] = 0;
            }
            if (!isset($cityList[$city]['availability'])) {
                $cityList[$city]['availability'] = 0;
            }
        }
        foreach ($users as $user) {

            $userCities = $user->tagsWithType('EligibleCity');
            foreach ($userCities as $city) {
                $cityDisplay = strtolower(trim( $city->name));
                $cityList[$cityDisplay]['name'] = $cityDisplay;
                if (isset($cityList[$cityDisplay]['userCount'])) {
                    $cityList[$cityDisplay]['userCount']++;
                } else {
                    $cityList[$cityDisplay]['userCount'] = 1;
                }
                if ($user->availability) {

                    if (isset($cityList[$cityDisplay]['availability'])) {
                        $cityList[$cityDisplay]['availability'] += $user->availability->count();
                    } else {
                        $cityList[$cityDisplay]['availability'] = $user->availability->count();
                    }
                }
                if (!isset($cityList[$cityDisplay]['closed'])) {
                    $cityList[$cityDisplay]['closed'] = 0;
                }
                if (!isset($cityList[$cityDisplay]['kwh'])) {
                    $cityList[$cityDisplay]['kwh'] = 0;
                }

                if (!isset($cityList[$cityDisplay]['appointments'])) {
                    $cityList[$cityDisplay]['appointments'] = 0;
                }
                if (!isset($cityList[$cityDisplay]['futureAppointments'])) {
                    $cityList[$cityDisplay]['futureAppointments'] = 0;
                }
                if (!isset($cityList[$cityDisplay]['sat'])) {
                    $cityList[$cityDisplay]['sat'] = 0;
                }
                if (!isset($cityList[$cityDisplay]['userCount'])) {
                    $cityList[$cityDisplay]['userCount'] = 0;
                }
                if (!isset($cityList[$cityDisplay]['availability'])) {
                    $cityList[$cityDisplay]['availability'] = 0;
                }
            }
        }

        $payloads = collect($cityList);
        $payloadArray = array();
        foreach ($payloads as $payload) {
            $payloadArray[] = $payload;
        }

        return $payloadArray;

    }

}
