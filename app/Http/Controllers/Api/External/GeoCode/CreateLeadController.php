<?php

namespace App\Http\Controllers\Api\External\GeoCode;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Epc\CompleteSiteSurveyQuestions;
use App\Models\Epc\Epc;
use App\Models\Office\Office;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\UserHasLead;
use Google\Service\PeopleService\Resource\People;
use Illuminate\Http\Request;

class CreateLeadController extends Controller
{

    public function store(Request $request)
    {

        $customer = new Customer();
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->street_address = $request->street_address;
        $customer->state = 'CA';
        $customer->city = $request->city;
        $customer->zip_code = $request->zip_code ?? '';
        $customer->lat = $request->lat;
        $customer->lng = $request->lng;
        $customer->save();

        $utility = new LeadUtility();
        $utility->save();
        $salesPacket = new SalesPacket();
        $salesPacket->save();


        $creditStatus = 1;

        $lead = new Lead();
        $lead->credit_status_id = $creditStatus;
        $lead->office_id = 5;
        $lead->origin_office_id = 5;
        $lead->customer_id = $customer->id;
        $lead->sales_packet_id = $salesPacket->id;
        $lead->utility_id = $utility->id;
        $lead->epc_id = 1;
        $lead->integrations_approved = 3;

        $lead->save();
        $siteSurveyQuestion = new CompleteSiteSurveyQuestions([
        ]);
        $lead->siteSurveyQuestions()->save($siteSurveyQuestion);

        $proposal = new RequestedSystem();
        $proposal->epc_finance_id = 395;
        $proposal->ppw = 4;
        $proposal->offset = 100;
        $proposal->solar_rate = .16;
        $proposal->lead_id = $lead->id;
        $proposal->save();


        $leadLogin = new LeadLogin();
        $leadLogin->lead_id = $lead->id;
        $leadLogin->user_name = ' ';
        $leadLogin->password = ' ';
        $leadLogin->save();


        $lead->save();
        if ($request->userEmail === 'chris@furman.tech') {
            $userEmail = 'chris@solving.solar';
        } else {
            $userEmail = $request->userEmail;
        }
        $user = User::where('email', $userEmail)->first();
        $opener = User::where('email', 'chris@solving.solar')->first();

        $rep = new UserHasLead();
        $rep->lead_id = $lead->id;


        $rep->user_id = $opener->id;
        $rep->position_id = 1;
        $rep->save();

        $rep = new UserHasLead();
        $rep->lead_id = $lead->id;
        $rep->user_id = $user->id;
        $rep->position_id = 2;
        $rep->save();
        $lead->source = 'self gen';
        $lead->save();

        //link to lead use app url and lead id use config instead of env
        $link = config('app.url') . '/dashboard/lead/' . $lead->id;

        return response()->json([
            'message' => 'Lead created successfully',
            'lead' => $lead,
            'link' => $link
        ], 201);
    }
}
