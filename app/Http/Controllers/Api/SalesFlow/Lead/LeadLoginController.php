<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\LoginUpdateEvent;
use App\Http\Resources\LoginResource;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadUtility;
use Illuminate\Http\Request;

class LeadLoginController
{

    public function index(Lead $lead, LeadUtility $leadUtility)
    {
        $login = LeadLogin::where('lead_id', $lead->id)->first();
        if ($login){
        return new LoginResource($login);
        }
    }


    public function update(Lead $lead, LeadUtility $leadUtility, LeadLogin $leadLogin, Request $request)
    {

        $leadLogin = LeadLogin::where('lead_id', $lead->id)
            ->first();

        $leadLogin->update($request->all());


//        gets only the changes
        $something = $leadLogin->getChanges();
        if (count($something) > 0) {

//        I need the ID for vue to match on the page
            $something['id'] = $leadLogin->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new LoginUpdateEvent($data, $lead->id));
        }
        return $something;
    }

    public function show(Lead $lead, LeadUtility $leadUtility, LoginResource $loginResource)
    {
        return new LoginResource($leadUtility);
    }
}
