<?php


namespace App\Http\Controllers\Api\RoundRobin;


use App\Http\Controllers\Controller;
use App\Http\Resources\RoundRobin\Office\UserResource;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use Illuminate\Http\Request;

class OfficeRoundRobinController extends Controller
{
    public function store(Office $office, Request $request)
    {
        $roundRobin = RoundRobin::where('office_id', $office->id)
            ->where('type', 'Call Center Appointments')
            ->pluck('list')
            ->toArray();


        array_push($roundRobin[0], $request->userId);
        RoundRobin::where('office_id', $office->id)
            ->where('type', 'Call Center Appointments')
            ->update(['list' => $roundRobin[0]]);


    }

    public function delete(Office $office, Request $request)
    {

        $roundRobin = RoundRobin::where('office_id', $office->id)
            ->where('type', 'Call Center Appointments')
            ->first();

        $list = $roundRobin->list;
        unset($list[array_search($request->userId, $list)]);
        $payloadArray = [];
        foreach ($list as $item) {
            array_push($payloadArray, $item);
        }
        $roundRobin->list = $payloadArray;
        $roundRobin->save();
        return $roundRobin;
    }

    public function cityListForUserRR($officeId)
    {

        $roundRobinList = RoundRobin::where('office_id', $officeId)
            ->where('type', 'Call Center Appointments')->first();

        $ids_ordered = implode(',', $roundRobinList->list);
        $users = User::whereIn('id', $roundRobinList->list)
            ->orderByRaw("FIELD(id, $ids_ordered)")
            ->get();
        foreach ($users as $user) {
            $cityArray = [];
            foreach ($user->tagsWithType('EligibleCity') as $city) {
                array_push($cityArray, $city->name);

            }
            $user->cities = $cityArray;
        }

        $usersWithList = $users->map(function ($item, $key) {
            return [
                'id' => $item['id'],
                'name' => $item['full_name'],
                'cities' => $item['cities'],
                'avatar' => $item['picture']
            ];
        });

        $array = [];
        $office = Office::where('id', $officeId)->first();
        foreach ($office->tagsWithType('EligibleCity') as $tag) {
            array_push($array, $tag->name);
        }

        return [
            'usersWithList' => $usersWithList,
            'selectedCities' => $array
        ];
        $userList = [];
        foreach ($officesWithList as $list) {
            foreach ($list['list'] as $l)
                array_push($userList, $l);
        }
        $list = array_unique($userList);

        $users = User::whereIn('id', $list)->get();

        return $payload = [
            'users' => UserResource::collection($users),
            'roundRobins' => $officesWithList
        ];


    }

}
