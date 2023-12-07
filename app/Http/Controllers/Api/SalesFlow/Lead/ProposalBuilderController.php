<?php

namespace App\Http\Controllers\Api\SalesFlow\Lead;

use App\Events\Backend\SalesFlow\Sp1AddedEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Http\Controllers\Backend\Twilio\TwilioSMSController;
use App\Http\Controllers\Controller;
use App\Http\Resources\RequestSP1Resource;
use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\UserHasLead;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class ProposalBuilderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Lead $lead, Request $request)
    {


        $body = null;


        switch ($request->type) {
            case 'panic':

//                $name = $request->user['name']['first'] . ' ' . $request->user['name']['last'];
//                $body = 'Hot lead ' . $name . ' needs help! ' . URL::to('/') . '/dashboard/lead/assign-sp1/' . $request->lead . '';
//                $this->textAlert($body, $request->office_id);
//                $lead->request_sp1 = true;
//                $lead->save();
                break;

            case 'requestProposalBuilder':
//
//                $name = $request->user['name']['first'] . ' ' . $request->user['name']['last'];
//                $body = 'Proposal Request' . $name . '. ' . URL::to('/') . '/dashboard/lead/assign-sp1/' . $request->lead . '';
//                $lead->request_sp1 = true;
//                $lead->save();
//                $this->textAlert($body);

                break;

            case 'checkSp1':
//                $user = UserHasLead::where(
//                    'lead_id', '=', $request->lead)
//                    ->where('position_id', '=', 2)
//                    ->with('user')
//                    ->get()
//                    ->first();
//
//                if ($user !== null) {
//                    if ($user->user_id == $request->userId) {
//                        $data = [
//                            'ok' => true,
//                            'user_name' => $user->user->full_name,
//                            'progress' => true,
//                            'message' => 'Is On their Way! ',
//                        ];
//                    } else {
//                        $data = [
//                            'ok' => true,
//                            'user_name' => $user->user->full_name,
//                            'progress' => true,
//                            'message' => 'Is On their Way! ',
//                        ];
//                    }
//                    return collect($data);
//                } else {
//                    $data = ['ok' => false];
//                    collect($data);
//                    return $data;
//                }
                break;

            case 'assignPB':
                $user = UserHasLead::where(
                    'lead_id', '=', $request->lead)
                    ->where('position_id', '=', 6)->with('user')->get()->first();
                if ($user == null) {
                    $newUser = new UserHasLead();
                    $newUser->user_id = $request->user;
                    $newUser->lead_id = $request->lead;
                    $newUser->position_id = 6;
                    $newUser->save();
                    $lead->request_builder = false;
                    $lead->save();

                    $username = User::where('id', '=', $request->user)->get()->first();

//                    event(new Sp1AddedEvent($request->lead, $username->full_name, $username->phone_number, 'assigned'));

                    $data = [
                        'ok' => true,
//							'user_name' => $user->user->full_name,
//                        'message' => 'Is On their Way!',
                    ];
                    return collect($data);

                } else {
                    $data = ['ok' => false,
                        'progress' => false];
                    collect($data);
                    return $data;
                }
                break;
        }

    }

    public function textAlert($body)
    {

        $reps = User::whereHas("positions", function ($q) {
            $q->where("position_id", "=", 6);
        })->get();

        foreach ($reps as $rep) {

            event(new TextEvent($rep->phone_number, $body));
            \Log::info('proposal builder text was sent');


        }

        return 'Sms Sent';
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */
    public function show($leadId, $sp1)
    {


        $user = UserHasLead::where(
            'lead_id', '=', $leadId)->where('position_id', '=', 2)->with('user')->get()->first();
        if (!$user == null) {
            return new RequestSP1Resource(UserHasLead::where(
                'lead_id', '=', $leadId)->where('position_id', '=', 2)->with('user')->get()->first());
        }

        return json_encode(json_decode("{'data': null}"));
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
