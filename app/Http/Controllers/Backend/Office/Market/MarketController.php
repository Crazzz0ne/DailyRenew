<?php

namespace App\Http\Controllers\Backend\Office\Market;


use App\Models\Office\Market\Market;
use App\Models\Office\Office;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class MarketController
{
    public function index()
    {
        $markets = Market::all();
        return view('backend.office.market.index', compact('markets'));
    }

    public function edit(Request $request, Market $market)
    {

        $permissions = Permission::where('id', '>', 0)->orderBy('name')->get();
        return view('backend.office.market.edit', compact('market', 'permissions'));
    }

    public function update(Request $request, Market $market)
    {

        $offices = $market->office;
        $market->permissions = $request->permissions;
        foreach ($offices as $office) {
            if (isset($office->permissions)) {
                $arry = array_merge($office->permissions, $request->permissions);
            }else{
                $arry = $request->permissions;
            }
            foreach ($office->User as $user) {
                $user->syncPermissions($arry);
            }
        }

        $market->name = $request->name;
        $market->save();


        return redirect()->back()->withFlashSuccess('Market Updated');;
    }
}
