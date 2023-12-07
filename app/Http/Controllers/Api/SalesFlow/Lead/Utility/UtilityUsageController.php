<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead\Utility;


use App\Http\Controllers\Controller;
use App\Http\Resources\Lead\Utility\UtilityUsageResource;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\Utility\UtilityUsage;
use Illuminate\Http\Request;

class UtilityUsageController extends Controller
{
    public function index(Lead $lead, LeadUtility $leadUtility)
    {

        return new UtilityUsageResource(UtilityUsage::firstOrCreate(['utility_id' => $lead->utility_id]));

    }

    public function store()
    {

    }

    public function show()
    {

    }

    public function update(Lead $lead, LeadUtility $leadUtility, UtilityUsage $utilityUsage, Request $request)
    {
       $update = $request->all();
//        $array = $request->toArray();
//            return $array;
      return UtilityUsage::where('utility_id', $lead->utility_id)->update($update);

    }

}
