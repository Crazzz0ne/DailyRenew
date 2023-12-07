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
        $leads = Lead::whereHas('office', function ($q) {
            $q->where('market_id', 14);
        })->with(['appointments' => function ($q) {
            $q->where('type_id', 6);
        },
            'system', 'customer'])->get();

        $users = User::whereHas('office', function ($q) {
            $q->where('market_id', 14);
        })->with(['availability' => function ($q) {
            $q->where('type', 'call-center')->where('start', '>', Carbon::now());
        }])->get();
        $cityList = [];
        foreach ($leads as $lead) {
            if ($lead->appointments->count()) {
                if (isset($cityList[$lead->customer->city]['appointments'])) {
                    $cityList[$lead->customer->city]['appointments']++;
                } else {
                    $cityList[$lead->customer->city]['appointments'] = 1;
                }
            }
            if ($lead->system) {
                if (isset($cityList[$lead->customer->city]['closed'])) {
                    $cityList[$lead->customer->city]['closed']++;
                } else {
                    $cityList[$lead->customer->city]['closed'] = 1;
                }
                if (isset($cityList[$lead->customer->city]['kwh'])) {
                    $cityList[$lead->customer->city]['kwh'] += $lead->system->system_size;
                } else {
                    $cityList[$lead->customer->city]['kwh'] = $lead->system->system_size;
                }
            }

        }

        foreach ($users as $user) {

            $userCities = $user->tagsWithType('EligibleCity');
            foreach ($userCities as $city) {
                if (isset($cityList[$city->name]['userCount'])) {
                    $cityList[$city->name]['userCount']++;
                } else {
                    $cityList[$city->name]['userCount'] = 1;
                }
                if ($user->availability){

                    if (isset($cityList[$city->name]['availability'])) {
                        $cityList[$city->name]['availability'] += $user->availability->count();
                    } else {
                        $cityList[$city->name]['availability'] = $user->availability->count();
                    }
                }
            }

        }

        return $cityList;
    }
}
