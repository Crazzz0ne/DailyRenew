<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;

use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\Auth\UserTokenResource;
use App\Models\Auth\User;
use App\Models\Auth\UserHasPosition;
use App\Models\Office\Office;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Lead\UserHasLead;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Junaidnasir\Larainvite\Facades\Invite;
use Redirect;
use Spatie\Tags\Tag;
use Storage;

class UserController extends Controller
{
    public function __construct(Request $request)
    {

    }

    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {


        return new UserResource($user);
    }

    public function index(Request $request)
    {
        $length = $request->input('length', 30);
        $users = $this->filterUsers($request);
        return UserResource::collection($users->with('roles', 'homeOffice')->paginate($length));
    }

    public function count(Request $request)
    {
        $users = $this->filterUsers($request);
        return $users->count();
    }

    private function filterUsers(Request $request)
    {
        $user = Auth::user();
        $users = User::query();

       if ($request->terminated === false || $request->terminated === "false") {
             $users->where('terminated', null);
        } else {
           $users->where('terminated', '!=', null);
        }

        if ($request->search) {
            $search = $request->search;
            $users->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', $search . '%')
                    ->orWhere('last_name', 'LIKE', $search . '%')
                    ->orWhere('email', 'LIKE', $search . '%')
                    ->orWhere('phone_number', 'LIKE', $search . '%');
            });
        }

        if ($request->position) {
            $users->whereHas("roles", function ($q) use ($request) {
                $q->where("name", $request->position);
            });
        }

        if ($user->hasAnyRole(['executive', 'administrator'])) {
            $this->filterForExecutiveAndAdmin($users, $request);
        } elseif ($user->hasAnyRole(['regional manager'])) {
            $this->filterForRegionalManager($users, $user);
        } elseif ($user->hasRole('manager')) {
            $users->where('office_id', $user->office_id);
        }

        return $users;
    }

    private function filterForExecutiveAndAdmin($query, $request)
    {
        if ($request->officeId !== null) {
            $query->where('office_id', $request->officeId);
        }
    }

    private function filterForRegionalManager($query, $user)
    {
        $userMarketId = Office::where('id', $user->office_id)->pluck('market_id')->first();
        $officeArray = Office::where('market_id', $userMarketId)->pluck('id')->toArray();
        $query->whereIn('office_id', $officeArray);
    }

    public function byPosition(Request $request)
    {
        if (!$request->officeId) {
            $officeId = Auth::user()->office_id;
        } else {
            $officeId = $request->officeId;
        }


        switch ($request->position) {
            case 'canvasser':
                if (Auth::user()->hasPermissionTo('accept d2d call center') || Auth::user()->hasPermissionTo('view office')) {
                    $officeId = Auth::user()->office_id;
                    $office = Office::where('id', $officeId)->first();
                    $users = User::role('canvasser')
                        ->whereHas('office', function ($q) use ($office) {
                            $q->where('market_id', $office->market_id);
                        })->where('terminated', null)->orderBy('first_name')->get();
                    $user = User::role('opener')->whereHas('office', function ($q) use ($office) {
                        $q->where('market_id', $office->market_id);
                    })->where('terminated', null)->orderBy('first_name')->get();
                    $users = $users->merge($user);

                } else {
                    $users = User::role('canvasser')->where('office_id', $officeId)->where('terminated', null)->orderBy('first_name')->get();
                    $user = User::role('opener')->where('office_id', $officeId)->where('terminated', null)->orderBy('first_name')->get();
                    $users = $users->merge($user);
                }

                break;
            case 'sp1':
                $users = User::role('sp1')->where('office_id', $officeId)->where('terminated', null)->orderBy('first_name')->get();
                break;
            case 'sp2':
                $users = User::role('sp2')->where('office_id', $officeId)->where('terminated', null)->orderBy('first_name')->get();
                break;
            case 'proposal builder':
                $users = User::role('proposal builder')->where('terminated', null)->orderBy('first_name')->get();
                break;
            case 'team captain':
                $users = User::role('team captain')->where('team_id', 0)->where('terminated', null)->orderBy('first_name')->get();
                break;
            case 'account manager':
                $users = User::role('account manager')->where('terminated', null)->orderBy('first_name')->get();
                break;
            default:
                return [];
        }


        $array = [];
        foreach ($users as $user) {
            $temp = [];
            $temp['label'] = $user->fullName;
            $temp['value'] = $user->id;

            array_push($array, $temp);
        }
        return $array;


        return $payload;


    }

    public function updateRoles(Request $request, User $user)
    {
        $apiUser = Auth::user();
        if ($apiUser->hasAnyRole(['executive', 'administrator', 'manager'])) {
            $user->syncRoles($request->roles);
            UserHasPosition::where('user_id', $user->id)->delete();
//            foreach ($request->roles as $position) {
//                switch ($position) {
//                    case 'canvasser':
//                        $newPosition = 1;
//                        break;
//                    case 'sp1':
//                        $newPosition = 2;
//                        break;
//                    case 'sp2':
//                        $newPosition = 3;
//                        break;
//                    case 'integrations':
//                        $newPosition = 4;
//                        break;
//                    case 'sales rep':
//                        $newPosition = 5;
//                        break;
//                    case 'proposal builder':
//                        $newPosition = 6;
//                        break;
//                }
//                if ($position) {
//                    UserHasPosition::create([
//                        'user_id' => $user->id,
//                        'position_id' => $newPosition]);
//                }
//            }
        }

    }


    public function updatAutoRR(Request $request, User $user)
    {
        $user->auto_assign_rr = $request->input('auto_assign_rr');
        $user->save();
        return $user;
    }

    public function updateOffice(Request $request, User $user)
    {
        $user->office_id = $request->input('office_id');
        $user->save();
        return $user;
    }

    public function eventList(Request $request)
    {
        $users = User::query();
        $users->where('terminated', null);

        if ($request->event === 'close') {
            $users->role('sp2');
        }

        if (Auth::user()->role('manager') && !Auth::user()->hasAnyRole(['executive', 'administrator', 'proposal builder', 'regional manager', 'pre sale', 'manager'])) {
            $regionalOfficeID = Auth::user()->office_id;
            $marketId = Office::where('id', $regionalOfficeID)->get()->pluck('market_id');
            $offices = Office::where('market_id', $marketId)->get()->pluck('id')->toarray();
            $users->whereIn('office_id', $offices);

//            $key .= '.selectedRegion.' . $marketId;
        }
        if (!Auth::user()->hasAnyRole(['executive', 'administrator', 'proposal builder', 'regional manager', 'pre sale', 'manager'])) {
            if ($request->event === 'follow-up' || $request->event === 'task') {
                $userHasLead = UserHasLead::where('lead_id', $request->leadId)->with('user')->get();
                foreach ($userHasLead as $user) {
                    $payload[] = ['label' => $user->user->fullName, 'value' => $user->user_id];
                }
                return $payload;
            } else {
                return [['label' => Auth::user()->getFullNameAttribute(), 'value' => Auth::user()->id]];

            }
        }
        $users = $users->get();
//return $users;
        $payload = [];
        foreach ($users as $user) {
            $payload[] = ['label' => $user->fullName, 'value' => $user->id];
        }

        return $payload;

    }


    public function updateLanguages(Request $request, User $user)
    {
        $user->languages = $request->input('languages');
        $user->save();

        return $user;
    }

    public function updatePayscale(Request $request, User $user)
    {
        if ($request->has('payscale')) {
            $user->payscale = $request->input('payscale');
            return $user->save();
        }

        return response("No Input Provided", 400);
    }

    public function updateTimezone(Request $request, User $user)
    {
        if ($request->has('timezone')) {
            $user->timezone = $request->input('timezone');
            return $user->save();
        }

        return response("No Input Provided", 400);
    }

    public function updateRemote(Request $request, User $user)
    {
        $user->remote_option = $request->input('remote_option');
        $user->save();

        return $user;
    }

    public function updateCities(Request $request, User $user)
    {
        $user->cities = $request->input('cities');
        $user->save();

        return $user;
    }

    public function show(Request $request, User $user)
    {
        $apiUser = Auth::user();

        if ($apiUser->hasAnyRole(['executive', 'administrator'])) {
            return new UserResource($user->where('id', $user->id)->with(['roles' => function ($q) {
                $q->orderBy('name');
            }])->with('homeOffice')->first());
        } elseif ($apiUser->hasAnyRole(['regional manager'])) {
            $userMarketId = Office::where('id', $user->office_id)->pluck('market_id')->first();
            $officeArray = Office::where('market_id', $userMarketId)->get()->pluck('id');
            if (in_array($user->office_id, $officeArray->toArray())) {
                return new UserResource($user->where('id', $user->id)->with(['roles' => function ($q) {
                    $q->orderBy('name');
                }])->with('homeOffice')->first());
            }
        } elseif ($apiUser->hasRole('manager') && $apiUser->office_id === $user->office_id) {
            return new UserResource($user->where('id', $user->id)->with(['roles' => function ($q) {
                $q->orderBy('name');
            }])->with('homeOffice')->first());
        }

    }

    public function goBackAvailable(Request $request)
    {
        $user = Auth::user();
        $roundRobin = RoundRobin::where('office_id', $user->office_id)->first();
        $roundRobinList = $roundRobin->list;
        $array = [];

        if (in_array($user->id, $roundRobinList)) {
            $position = array_search($user->id, $roundRobinList);
            unset($roundRobinList[$position]);

            foreach ($roundRobinList as $list) {
                array_push($array, $list);
            }
            $roundRobin->list = $array;
        } else {
            array_push($roundRobinList, $user->id);
            $roundRobin->list = $roundRobinList;
        }
        $roundRobin->save();
        return $roundRobin;
    }

    public function getUser(Request $request)
    {
        $key = $request->api_token;
        if (!$request->api_token) {
            return Redirect::back();
        }
        $payload = Cache::remember($key, 600, function () use ($key) {
            return User::where('api_token', $key)->with('homeOffice')->first();
        });
        return new UserTokenResource($payload);
    }

    public function store(Request $request)
    {
        $apiUser = Auth::user();

        $refCodes = [];

        if ($apiUser->hasAnyRole(['executive', 'administrator', 'manager'])) {
            $emails = explode(',', $request->users);
            foreach ($emails as $email) {
                $trimmedEmail = trim($email);
                $refCode = Invite::invite($trimmedEmail, $apiUser->id);
                array_push($refCodes, $refCode);
            }
            return $refCodes;
        }
    }

    public function office(Request $request)
    {


        if ($request->office_id) {
            $officeId = $request->office_id;
        } else {
            $user = Auth::user();

            $officeId = $user->office_id;
        }
        $key = 'userInoffice.' . $officeId;
        return Cache::remember($key, 600, function () use ($officeId) {

            $officeId = (int)$officeId;
            return UserResource::collection(User::where('office_id', $officeId)->where('active', true)->orderBy('first_name', 'asc')->get());
        });
    }

    public function terminate(User $user, Request $request)
    {
        $apiUser = Auth::user();
        $can = 0;

        if ($apiUser->hasAnyRole(['executive', 'administrator'])) {
            $can = 1;
        } elseif ($apiUser->hasRole('manager') && $apiUser->office_id === $user->office_id) {
            $can = 1;
        }

        if ($can) {
            if (!$user->terminated) {
                $user->terminated = Carbon::now()->toDateTimeString();
                $user->active = false;
                $user->to_be_logged_out = true;
            } else {
                $user->terminated = null;
                $user->active = true;
            }
            $user->save();
        }
        return $user;
    }

    public function selectedCities(User $user)
    {

        $array = [];
        foreach ($user->tagsWithType('EligibleCity') as $tag) {
            array_push($array, $tag->name);
        }
        sort($array);
        return $array;
    }

    public function storeSelectedCities(User $user, Request $request)
    {

        $user->syncTagsWithType($request->city, 'EligibleCity');
        return 'good';
    }

    public function batchCity(User $user, Request $request)
    {
        // $request->cities is a csv, let's convert it to an Array

        $file = $request->file('cities'); // retrieve the uploaded file
        $path = $file->store('temp'); // store the file in a temporary directory
        $contents = Storage::get($path); // read the contents of the file

        // You can then convert the CSV string to an array using the preg_split() function
        $cities = preg_split('/\r\n|\r|\n/', $contents);

        foreach ($cities as $city) {
//            check if city is empty
            if (empty($city)) {
                continue;
            }
            Tag::findOrCreate($city, 'EligibleCity');
            $user->attachTag($city, 'EligibleCity');
        }

        Storage::delete($path); // delete the temporary file after processing

        return 'good';
    }
}
