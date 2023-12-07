<?php


namespace App\Http\Controllers\Api\Office;


use App\Http\Controllers\Controller;
use App\Http\Resources\OfficeStandingResource;
use App\Http\Resources\UserResource;
use App\Models\Auth\User;
use App\Models\Office\Office;
use App\Models\Office\OfficeStanding;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OfficeUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Office $office, Request $request)
    {
        $user = User::query();

        $user->where('office_id', $office->id)
            ->where('terminated', null)
            ->with('roles');

//Finds open team members
        if ($request->team) {
            $user->where('team_id', '=', 0);
        }
        $user = $user->get();

        return UserResource::collection($user);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param String
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param String $month
     * @return Response
     */
    public function showMonth(string $month)
    {
        $start = Carbon::parse($month)->startOfMonth()->toDateString();
        $end = Carbon::parse($month)->endOfMonth()->toDateString();

        $links = OfficeStanding::whereBetween('sdate', [$start, $end])
            ->where('approved', '=', 1)
            ->with(['data.office'])
            ->get();

        return OfficeStandingResource::collection($links);

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
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
