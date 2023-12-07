<?php

namespace App\Http\Controllers\Backend\Office;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoundRobin\Office\UserResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\Role;
use App\Models\Auth\User;
use App\Models\Office\Market\Market;
use App\Models\Office\Office;
use App\Models\Office\OfficeOptions;
use App\Models\RoundRobin\RoundRobin;
use App\Repositories\Backend\Auth\OfficeRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Log;
use Spatie\Permission\Models\Permission;


class OfficeController extends Controller
{
    protected $officeRepository;

    public function __construct(OfficeRepository $officeRepository)
    {
        $this->officeRepository = $officeRepository;
//        $this->middleware(['role_or_permissions:administrate|executive|administrate all offices', 'office_owner:id'])->only([ 'edit', 'create', 'store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        if (\Auth::user()->hasPermissionTo('administrate company')) {
            $offices = Office::all();
        } elseif (\Auth::user()->hasRole('regional manager')) {
            $userOffice = Office::where('id', \Auth::user()->office_id)->pluck('market_id')->first();
            $offices = Office::where('market_id', $userOffice)->get();
        } else {
            $offices = Office::where('id', \Auth::user()->office_id)->get();
        }

        return view('backend.office.index', compact('offices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $states = [
            'AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA',
            'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA',
            'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND',
            'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT',
            'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY'
        ];
        $markets = Market::all()->pluck('name', 'id');


        return view('backend.office.create', compact('states', 'markets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:offices|max:100',

        ]);

        if ($validator->fails()) {
            return redirect('dashboard/office/create')
                ->withErrors($validator)
                ->withInput();
        }
        $office = new Office();
        $office->name = $request->name;
        $office->address = $request->address;
        $office->city = $request->city;
        $office->zip_code = $request->zipCode;
        $office->state = $request->state;
        $office->market_id = $request->market;
        $office->email = $request->email;
        $office->phone_number = $request->phone;
        $office->save();

        $options = new OfficeOptions();

        $options->office_id = $office->id;
        return redirect()->route('dashboard.office.index')->withFlashSuccess(__('Office Created!'));

    }

    /**
     * Display the specified resource.
     *
     * @param Office $office
     * @return Response
     */
    public function show(Office $office)
    {
        if (\Auth::user()->office_id !== $office->id) {
            return 'Nope';
        }

        $officeUser = Office::with('user')->where('id', $office->id)->get();
        return view('backend.office.show', compact('office', 'officeUser'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Office $office
     * @return Response
     */
    public function edit(Office $office)
    {
        $user = \Auth::user();
        if ($user->hasPermissionTo('administrate company')) {

        } else if ($user->hasRole('regional manager')) {
            $userMarketId = Office::where('id', $user->office_id)->pluck('market_id')->first();
            $leadMarketId = $office->market_id;

            if ($leadMarketId !== $userMarketId) {
                Mail::to('chris.furman@solcalenergy.com')
                    ->queue(new BaseMailable('Someone went where they shouldnt', $user, 'nowhere', 'lead'));

                return 'nope';
            }

        } else if ($user->hasRole('manager') && $user->office_id === $office->id) {

        } else {
            Log::alert('UserId ' . \Auth::user()->id . ' tried going to the wrong office to edit it. ');
            return redirect()->back();
        }
        $states = [
            'AL', 'AK', 'AS', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FM', 'FL', 'GA',
            'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA',
            'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND',
            'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT',
            'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY'
        ];
        $permissions = Permission::where('id', '>', 0)->orderBy('name')->get();
        $markets = Market::all()->pluck('name', 'id');
        $roles = Role::where('id', '!=', 1)->where('id', '!=', 2)->where('id', '!=', 3)->get();

        $options = OfficeOptions::where('office_id', $office->id)->first();

        return view('backend.office.edit', compact('office', 'states', 'markets', 'permissions', 'roles', 'options'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Office $office
     * @return Response
     */
    public function update(Request $request, Office $office)
    {
//        return $request;
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',

        ]);

        if ($validator->fails()) {
            return redirect('dashboard/office/edit')
                ->withErrors($validator)
                ->withInput();
        }


//        $this->officeRepository->update($office, $request->only(
//            'name',
//            'last_name',
//            'email',
//            'roles',
//            'permissions'
//        ));
        $update = Office::find($office->id);


        $market = $office->market;
        $options = OfficeOptions::where('office_id', $office->id)->first();

        if (isset($market->permissions)) {
            $arry = array_merge($market->permissions, $request->permissions);
        } else {
            $arry = $request->permissions;
        }
        foreach ($office->User as $user) {
            $user->syncPermissions($arry);
        }
//        return $request->permissions;
        $options->permissions = $request->permissions;
        $options->roles = $request->roles;
        $options->save();

        $update->require_integrations = (bool)$request->require_integrations;
        $update->name = $request->name;
        $update->address = $request->address;
        $update->city = $request->city;
        $update->state = $request->state;
        $update->market_id = $request->market;
        $update->phone_number = $request->phone;
        $update->email = $request->email;
        $update->call_center = $request->call_center;
        $update->save();


        return redirect()->route('dashboard.office.index')->withFlashSuccess(__('Office Updated!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Office $office
     * @return Response
     */
    public function destroy(Office $office)
    {
        if (Office::destroy($office->id)) {
            Log::info(auth()->user()->getAuthIdentifierName() . ' deleted a user');
            return redirect()->route('dashboard.office.index')->withFlashSuccess(__('Office Deleted!'));
        } else {
            Log::critical('Something has gone wrong with offices');
        }

    }
}
