<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Mail\SalesFlow\DIdNotCloseMailable;
use App\Models\Auth\User;
use App\Models\Epc\SolarModule;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;

use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class TestingController extends Controller
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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
