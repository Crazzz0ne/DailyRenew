<?php


namespace App\Http\Controllers\Api\Office;


use App\Http\Controllers\Controller;
use App\Http\Resources\OfficeResource;
use App\Http\Resources\Roles\RolesResource;
use App\Http\Resources\RoundRobin\Office\UserResource;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\Office\OfficeOptions;
use App\Models\RoundRobin\RoundRobin;
use Illuminate\Http\Request;


class OfficeController extends Controller
{

    public function index(Request $request)
    {
        if (\Auth::user()->hasRole('regional manager')) {
            $marketId = Office::where('id', \Auth::user()->office_id)->pluck('market_id')->first();
            return OfficeResource::collection(Office::where('market_id', $marketId)->where('active', true)->get());
        } elseif (\Auth::user()->hasAnyRole(['executive', 'administrator', 'integrations', 'proposal builder'])) {

            if ($request->market_id) {
                return OfficeResource::collection(Office::where('market_id', $request->market_id)->where('active', true)->get());
            }else{
               return OfficeResource::collection(Office::where('active', true)->get());
            }
        }
    }

    public function role(Office $office)
    {
        $roles = OfficeOptions::where('office_id', $office->id)->first();
        return $roles->roles;
    }


}
