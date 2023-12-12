<?php

namespace App\Http\Controllers\Api\RoundRobin;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoverageController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::whereHas('office', fn($q) => $q->where('market_id', 1))
            ->with(['appointments' => fn($q) => $q->where('type_id', 6), 'system', 'customer'])
            ->get();

        $users = User::whereHas('office', fn($q) => $q->where('market_id', 1))
            ->with(['availability' => fn($q) => $q->where('type', 'call-center')->where('start', '>', Carbon::now())])
            ->get();

        $cityList = $this->processLeads($leads);
        $cityList = $this->processUsers($users, $cityList);

        return $cityList;
    }

    private function processLeads($leads)
    {
        $cityList = [];
        foreach ($leads as $lead) {
            $city = $lead->customer->city;
            $cityList[$city]['appointments'] = ($cityList[$city]['appointments'] ?? 0) + $lead->appointments->count();
            if ($lead->system) {
                $cityList[$city]['closed'] = ($cityList[$city]['closed'] ?? 0) + 1;
                $cityList[$city]['kwh'] = ($cityList[$city]['kwh'] ?? 0) + $lead->system->system_size;
            }
        }
        return $cityList;
    }

    private function processUsers($users, $cityList)
    {
        foreach ($users as $user) {
            foreach ($user->tagsWithType('EligibleCity') as $city) {
                $cityName = $city->name;
                $cityList[$cityName]['userCount'] = ($cityList[$cityName]['userCount'] ?? 0) + 1;
                $cityList[$cityName]['availability'] = ($cityList[$cityName]['availability'] ?? 0) + $user->availability->count();
            }
        }
        return $cityList;
    }
}
