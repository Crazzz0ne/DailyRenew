<?php

namespace App\Http\Controllers\Api\SalesFlow\Customer;


use App\Events\Backend\SalesFlow\Lead\CustomerEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\Lead\LineResource;
use App\Models\Auth\User;
use App\Models\Epc\CompleteSiteSurveyQuestions;
use App\Models\Epc\Epc;

use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadLogin;
use App\Models\SalesFlow\Lead\LeadUtility;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\SalesPacket;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;

use App\Services\GeoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\URL;


class CustomerController extends Controller
{
    protected $leadRepository;
    protected $geoService;

    public function __construct(lead $lead, GeoService $geoService)
    {
        $this->leadRepository = new LeadRepository();
        $this->geoService = $geoService;
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {

        $goBacks = Line::where('type', 'go back')->where('filled_user_id', '=', null)
            ->whereHas('lead.appointments', function ($q) {
                $twentyMinutes = Carbon::now()->addMinutes(20);
                $q->where('type_id', 8);
                $q->where('start_time', '<', $twentyMinutes);
            })
            ->with('lead.appointments', 'lead.customer')->get();

        $roundRobin = RoundRobin::where('type', 'Go Back')->get();
        foreach ($goBacks as $followup) {
            $list = $roundRobin->where('office_id', $followup->lead->office_id)->first();
            foreach ($list->list as $eligibleUser) {
                $filledTime = Appointment::where('user_id', $eligibleUser)
                    ->whereBetween('start_time', [Carbon::now()->toDateTimeString(), Carbon::now()->addHour()->toDateTimeString()])
                    ->count();
                if ($filledTime === 0) {
                    $user = User::find($eligibleUser);


                    $body = "Scheduled Go back @" . $followup->lead->appointments[0]->start_time . "\nZip " . $followup->lead->customer->zip_code . " "
                        . URL::to("/") . "/dashboard/queue/" . $followup->id;
//                    event(new TextEvent($user->phone_number, $body));

                    $array = $list->list;
                    array_shift($array);
                    array_push($array, $user->id);

                    $roundRobin[0]->list = $array;
                    $roundRobin[0]->save();
                    break;

                }

            }

        }

    }

    public function geoLocation(Customer $customer, Request $request)
    {
        $data = $this->geoService->sendData($customer->lat, $customer->lng, $request->radius, $request->filter);
        return ['data' => $data];
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

    }

    public function multiLead(Customer $customer)
    {
        $leads = Lead::where('customer_id', '=', $customer->id)
            ->with('epc')
            ->get();
        $leadEpc = [];
        $i = 0;
        foreach ($leads as $lead) {

            $epc = Epc::where('id', $lead->epc_id)->first();

            $leadArray[$i]['label'] = $epc->name;
            $leadArray[$i]['value'] = $lead->id;
            $i++;
        }
        $data = ['data' => $leadArray];
        return collect($data);
    }


    public function store(Request $request)
    {
        $user = \Auth::user();
        $nameArray = explode(' ', $request->customer['full_name']);

        if (isset($request->customer['full_name'])) {
            $firstName = $nameArray[0];

            if (isset($nameArray[1])) {
                $lastName = $nameArray[1];

            } else {

                $lastName = 'not set';
            }

        } else {
            $firstName = 'not set';
            $lastName = 'not set';
        }


        $userId = $user->id;

        $customer = new Customer();
        $customer->first_name = $firstName;
        $customer->last_name = $lastName;
        $customer->street_address = $request->customer["address"]['street_address'] ?? '';
        $customer->state = $request->customer["address"]['state'] ?? '';
        $customer->city = $request->customer["address"]['city'] ?? '';
        $customer->zip_code = $request->customer["address"]['zip'] ?? '';
        $customer->home_phone = $request->customer["home_phone"] ?? '';
        $customer->cell_phone = $request->customer["cell_phone"] ?? '';
        $customer->email = $request->customer["email"] ?? '';
        $customer->language = $request->customer["language"] ?? '';
        $customer->lat = $request->customer["address"]["lat"] ?? '';
        $customer->lng = $request->customer["address"]["lng"] ?? '';
        $customer->save();


        $office = Office::where('id', $user->office_id)->first();

        $powerCompany = Epc::where('id', '=', $request->epc_id)
            ->with('powerCompany')
            ->get()
            ->pluck('powerCompany.id');

        $utility = new LeadUtility();
        if (isset($powerCompany[0])) {
            $utility->power_company_id = $powerCompany[0];
        }
        $utility->save();

        $salesPacket = new SalesPacket();
        $salesPacket->save();


        //Hard code from database.
        if ($request->epc === 1) {
//            env('APP_ENV') == 'production'
            if (true) {

                $finOption = 10;
                $creditStatus = 1;
            } else {
                $finOption = 1;
                $creditStatus = 1;
            }
        } else {
            $finOption = 11;
            $creditStatus = 4;
        }

        if ($request->opener) {
            $userId = $request->opener;
        }
        $opener = User::where('id', $userId)->first();


        $lead = new Lead();
        $lead->credit_status_id = $creditStatus;
        $lead->office_id = $user->office_id;
        $lead->origin_office_id = $user->office_id;
        $lead->customer_id = $customer->id;
        $lead->sales_packet_id = $salesPacket->id;
        $lead->utility_id = $utility->id;
        $lead->epc_id = $request->epc;
        $lead->save();
        $siteSurveyQuestion =  new CompleteSiteSurveyQuestions([
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

        if ($office->require_integrations) {
            $lead->integrations_approved = 1;
        } else {
            $lead->integrations_approved = 3;
        }

        $lead->save();

        if ($user->hasAnyRole(['sp1', 'sp2'])) {

            $rep = new UserHasLead();
            $rep->lead_id = $lead->id;

            if ($request->opener) {
                $rep->user_id = $request->opener;
            } else {
                $rep->user_id = $userId;
            }
            $rep->position_id = 1;
            $rep->save();

            $rep = new UserHasLead();
            $rep->lead_id = $lead->id;
            $rep->user_id = \Auth::id();
            $rep->position_id = 2;
            $rep->save();

            if ($lead->office->call_center) {
                $lead->source = 'call center';
            } else {
                $lead->source = 'self gen';
            }
            $lead->save();

        }  else if ($user->hasRole('canvasser')) {

            $rep = new UserHasLead();

            $rep->lead_id = $lead->id;
            $rep->user_id = $userId;
            $rep->position_id = 1;
            $rep->save();

        } else {
            $lead->source = 'call center';
            $lead->save();
        }

        return [
            'customer_id' => $customer->id,
            'lead_id' => $lead->id,
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param Customer $customer
     * @return Customer
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Customer $customer
     * @param Request $request
     * @return Customer
     */
    public function update(Customer $customer, Request $request)
    {
        if ($request->dob) {
            $customer->dob = Carbon::parse($request->dob)->toDateTimeString();
            $customer->save();
        } else {
            $customer->update($request->all());
        }
        //        gets only the changes
        $something = $customer->getChanges();
        if (count($something) > 0) {

            $lead = Lead::where('customer_id', $customer->id)->first();

//        I need the ID for vue to match on the page
            $something['id'] = $customer->id;
            $data = collect($something);
//        lets everyone else know of the changes
            event(new CustomerEvent($data, $customer->id, $lead->id));


        }
        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public
    function destroy($id)
    {
        //
    }
}
