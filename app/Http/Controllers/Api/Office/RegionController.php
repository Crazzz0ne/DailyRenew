<?php


namespace App\Http\Controllers\Api\Office;


use App\Http\Controllers\Backend\Office\Market\MarketController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Market\RegionListResource;
use App\Models\Office\Market\Market;

class RegionController extends Controller
{
    public function index()
    {
        return RegionListResource::collection(Market::where('active', true)->get());
    }

    public function users(Market $market)
    {
        $offices = $market->office;
        $userArray = [];
        foreach ($offices as $office) {
            foreach ($office->User as $user) {
                array_push($userArray, ['id' => $user->id, 'name' => $user->fullName]);
            }
        }
        return collect($userArray);

    }

}
