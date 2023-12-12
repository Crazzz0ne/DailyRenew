<?php


namespace App\Http\Controllers\Api\RoundRobin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoundRobin\Office\UserResource;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoundRobinController extends Controller
{
    public function markGoBackAvailable(Request $request)
    {
        $apiKey = substr($request->header('Authorization'), -80);
        $user = User::where('api_token', $apiKey)
            ->first();

        $roundRobin = RoundRobin::where('office_id', $user->office_id)->where('type', 'Go Back')->first();

        $roundRobin->list = array_push($roundRobin->list, $user->id);
        $roundRobin->save();


    }

    public function inGoBack(Request $request)
    {
        return 'yes';
        $apiKey = substr($request->header('Authorization'), -80);
        $user = User::where('api_token', '=', $apiKey)
            ->first();
        if ($user->office_id === 9 || $user->office_id === 6) {
            return 0;
        }
        $roundRobin = RoundRobin::where('office_id', $user->office_id)->first();

        if (in_array($user->id, $roundRobin->list)) {
            $data = ['data' => true];
            return collect($data);
        } else {
            $data = ['data' => false];
            return collect($data);
        }

    }

    public function index()
    {
        $roundRobinList = RoundRobin::where('office_id', 1)
            ->where('type', 'Call Center Offices')
            ->first();
        if (!$roundRobinList){
            return $roundRobinList;
        }

        $ids_ordered = implode(',', $roundRobinList->list);
        $offices = Office::whereIn('id', $roundRobinList->list)
            ->HasRoundRobinType('Call Center Appointments')
            ->with(['roundRobin' => function ($q) {
                $q->where('type', 'Call Center Appointments');
            }])->orderByRaw("FIELD(id, $ids_ordered)")
            ->get();

        $officesWithList = $offices->map(function ($item, $key) {
            if ($item['RoundRobin'][0]['type'] === 'Call Center Appointments') {
                return [
                    'id' => $item['RoundRobin'][0]['id'],
                    'office_id' => $item['id'],
                    'name' => $item['name'],
                    'userList' => $item['RoundRobin'][0]['list'],
                ];
            } else if (isset($item['RoundRobin'][1]) && $item['RoundRobin'][1]['type'] === 'Call Center Appointments') {
                return [
                    'id' => $item['RoundRobin'][1]['id'],
                    'office_id' => $item['id'],
                    'name' => $item['name'],
                    'userList' => $item['RoundRobin'][1]['list'],
                ];
            } else if ($item['RoundRobin'][0]['type'] === 'Call Center Offices' || (isset($item['RoundRobin'][1]) && $item['RoundRobin'][1]['type'] === 'Call Center Offices')) {
                return [
                    'id' => $item['RoundRobin'][1]['id'],
                    'office_id' => $item['id'],
                    'name' => $item['name'],
                    'userList' => [],
                ];
            }
        });
        $userList = [];
        foreach ($officesWithList as $list) {
            foreach ($list['userList'] as $l)
                array_push($userList, $l);
        }
        $list = array_unique($userList);
        $usersList = User::whereIn('id', $list)->get();
        return $payload = [
            'users' => UserResource::collection($usersList),
            'roundRobins' => $officesWithList
        ];
    }

    public function update(RoundRobin $roundRobin, Request $request)
    {
        $roundRobin = RoundRobin::where('office_id', 33)
            ->where('type', 'Call Center Round Robin')
            ->get();


    }

    public function store(RoundRobin $roundRobin, Request $request)
    {

            $roundRobin = RoundRobin::where('office_id', 1)
                ->where('type', 'Call Center Offices')
                ->first();
        $list = $roundRobin->list;
        if (!in_array($request->officeId, $list)) {
            $oldRR = RoundRobin::where('office_id', $request->officeId)
                ->where('type', 'Call Center Appointments')
                ->count();

            if ($oldRR == 0) {
                $users = User::role('sp2')->where('office_id', $request->officeId)->get()->pluck('id');

                $newRR = new RoundRobin();
                $newRR->office_id = $request->officeId;
                $newRR->type = 'Call Center Appointments';
                $newRR->list = $users;
                $newRR->save();

            }
        }

        $list = array_unique($list);
        array_push($list, (int)$request->officeId);
        $roundRobin->update(['list' => $list]);
    }

    public function delete(Request $request)
    {


    }


}
