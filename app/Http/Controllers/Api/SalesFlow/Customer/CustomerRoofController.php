<?php

namespace App\Http\Controllers\Api\SalesFlow\Customer;

use App\Http\Resources\RoofTypeResource;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadRoof;
use App\RoofType;
use Illuminate\Http\Request;

class CustomerRoofController
{
    public function index()
    {
        $roofs = RoofTypeResource::collection(RoofType::all());
        return response($roofs, 200);
    }

    public function store(Request $request, Lead $lead)
    {

        $relationship = LeadRoof::where('lead_id', $lead->id)->onlyTrashed();

        if ($relationship->count()) {
            $relationship->restore();
            return $relationship = LeadRoof::where('lead_id', $lead->id)->first();
        }


        if ($request->has('age') && $request->has('type')) {


//            \Log::info("Existing Lead Roof: $relationship");
            if ($lead->leadRoof) {
                 $lead->leadRoof->update([
                    'roof_type_id' => $request->input('type'),
                    'age' => $request->input('age')
                ]);
                 return $lead->leadRoof;
            }


             LeadRoof::create([
                'lead_id' => $lead->id,
                'roof_type_id' => $request->input('type'),
                'age' => $request->input('age')
            ]);
            return $lead->fresh()->leadRoof;
        } else {
            \Log::error("Failed To Create New Roof for {$lead->id}", [$request->input()]);
            return response("Error: Invalid Input", 409);
        }
    }

    public function delete(Lead $lead)
    {
        $relationship = LeadRoof::where('lead_id', $lead->id)->first();
        \Log::info("Deleting: ", [$relationship]);
        return $relationship->delete();
    }
}
