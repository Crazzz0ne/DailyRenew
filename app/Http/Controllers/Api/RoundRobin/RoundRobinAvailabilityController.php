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


        $officeRoundRobin = RoundRobin::where('office_id', 1)
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
        $leads = Lead::where('source', 'call center')
            ->whereHas('office', fn($q) => $q->where('market_id', 1))
            ->whereHas('appointments', fn($q) => $q->where('remote', false)->where('type_id', 6))
            ->with(['appointments', 'system', 'customer', 'salesPacket'])
            ->get();
        $users = $this->cityListForUserRR();
        $users = $users->load(['availability' => fn($q) => $q->where('type', 'in-person')
            ->where('start', '>', Carbon::now())]);

//        $users = User::whereHas('office', fn($q) => $q->where('market_id', 1))


        $cityList = [];
        foreach ($leads as $lead) {
            if (!$lead->customer || $lead->customer->city === '') {
                continue;
            }

            $city = strtolower(trim($lead->customer->city));
            if (!isset($cityList[$city])) {
                $cityList[$city] = [
                    'name' => $city,
                    'appointments' => 0,
                    'futureAppointments' => 0,
                    'closed' => 0,
                    'kwh' => 0,
                    'sat' => 0,
                    'userCount' => 0,
                    'availability' => 0,
                ];
            }

            $cityList[$city] = $this->updateCityListWithLead($cityList[$city], $lead);
        }

        foreach ($users as $user) {
            foreach ($user->tagsWithType('EligibleCity') as $city) {
                $cityDisplay = strtolower(trim($city->name));
                if (!isset($cityList[$cityDisplay])) {
                    $cityList[$cityDisplay] = [
                        'name' => $cityDisplay,
                        'appointments' => 0,
                        'futureAppointments' => 0,
                        'closed' => 0,
                        'kwh' => 0,
                        'sat' => 0,
                        'userCount' => 0,
                        'availability' => 0,
                    ];
                }

                $cityList[$cityDisplay] = $this->updateCityListWithUser($cityList[$cityDisplay], $user);
            }
        }

        return array_values($cityList);
    }

    private function generateCityList($leads, $users)
    {
        $cityList = [];

        foreach ($leads as $lead) {
            if (!$lead->customer || $lead->customer->city === '') {
                continue;
            }

            $city = strtolower(trim($lead->customer->city));
            $cityList[$city] = $this->updateCityListWithLead($cityList[$city] ?? [], $lead);
        }

        foreach ($users as $user) {
            foreach ($user->tagsWithType('EligibleCity') as $city) {
                $cityDisplay = strtolower(trim($city->name));
                $cityList[$cityDisplay] = $this->updateCityListWithUser($cityList[$cityDisplay] ?? [], $user);
            }
        }

        return $cityList;
    }

    private function updateCityListWithLead($cityData, $lead)
    {
        $cityData['name'] = strtolower(trim($lead->customer->city));
        $cityData['appointments'] = $lead->appointments->count() ? ($cityData['appointments'] ?? 0) + 1 : ($cityData['appointments'] ?? 0);
        $cityData['futureAppointments'] = $lead->appointments->count() && Carbon::parse($lead->appointments[0]->start_time) > Carbon::now() ? ($cityData['futureAppointments'] ?? 0) + 1 : ($cityData['futureAppointments'] ?? 0);
        $cityData['closed'] = $lead->system ? ($cityData['closed'] ?? 0) + 1 : ($cityData['closed'] ?? 0);
        $cityData['kwh'] = $lead->system ? ($cityData['kwh'] ?? 0) + $lead->system->system_size : ($cityData['kwh'] ?? 0);
        $cityData['sat'] = $lead->salesPacket->sat ? ($cityData['sat'] ?? 0) + 1 : ($cityData['sat'] ?? 0);

        return $cityData;
    }

    private function updateCityListWithUser($cityData, $user)
    {
        $cityData['userCount'] = ($cityData['userCount'] ?? 0) + 1;
        $cityData['availability'] = ($cityData['availability'] ?? 0) + $user->availability->count();

        return $cityData;
    }

}
