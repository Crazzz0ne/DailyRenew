<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\UtilityUpdateEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\UtilityUpdateRequest;
use App\Http\Resources\Lead\Utility\LeadUtilityResource;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUtility;
use Symfony\Component\HttpFoundation\Request;

class LeadUtilityController extends Controller
{
    //TODO: Why does the route model binding not work here?
    //If I remove the LeadUtility lookup the database does not update. Seems like a larger underlying issue.

    public function update(Lead $lead, LeadUtility $leadUtility, UtilityUpdateRequest $request)
    {
        $leadUtility = LeadUtility::where('id', '=', $lead->utility_id)->first();
        $leadUtility->update($request->all());

//        gets only the changes
        $something = $leadUtility->getChanges();
        if (count($something) > 0) {

//        I need the ID for vue to match on the page
            $something['id'] = $leadUtility->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new UtilityUpdateEvent($data, $lead->id));
        }
        return $something;
    }

    public function show(Lead $lead, LeadUtility $leadUtility)
    {
        $leadUtility = LeadUtility::where('id', '=', $lead->utility_id)->first();
        return new LeadUtilityResource($leadUtility);
    }
}
