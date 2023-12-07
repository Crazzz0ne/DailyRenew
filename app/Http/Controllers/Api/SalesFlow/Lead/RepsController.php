<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Events\Backend\SalesFlow\RepAddedToLeadEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\UpdateZapierEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\LeadNotesResource;
use App\Http\Resources\LeadRepsResource;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Models\Auth\User;
use App\Models\SalesFlow\Position\Position;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class RepsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Lead $lead
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Lead $lead, Request $request)
    {
        if (\Auth::user()->hasRole(['administrator','manager', 'proposal builder', 'executive', 'integrations', 'account manager'])) {
            $users = UserHasLead::where('lead_id', '=', $lead->id)->where('deleted_at', '=', null)->with('users', 'position', 'users.partnerLink.passwords')->get();
        }else{
            $users = UserHasLead::where('lead_id', '=', $lead->id)->where('deleted_at', '=', null)->with('users', 'position')->get();

        }
        return LeadRepsResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request ,
     * @return Response
     */
    public function store(Request $request)
    {


        $requestingUser = \Auth::user();
        $position = null;
        switch ($request->position) {
            case 'canvasser':
                $position = 1;
                break;
            case 'sp1':
                $position = 2;
                break;
            case 'sp2':
                $position = 3;
                break;
            case 'proposal builder':
                $position = 6;
                break;
            case 'account manager':
                $position = 9;
                break;
            default:
                $position = null;
                break;

        }

        $rep = new UserHasLead();
        $rep->lead_id = $request->lead_id;
        $rep->user_id = $request->user_id;
        $rep->position_id = $position;

        $rep->save();

        $user = User::where('id', '=', $rep->user_id)->first();
        $position = Position::where('id', '=', $position)->first();

        $data = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'position_name' => $position->name,
            'position_id' => $rep->position_id,

        ];
        $line = null;
        if ($position === 2) {
            $line = Line::where('type', 'sp1')
                ->where('lead_id', $rep->lead_id)
                ->where('filled_time', null)
                ->first();
        } else if ($position === 3) {
            $line = Line::where('type', 'sp2')
                ->where('lead_id', $rep->lead_id)
                ->where('filled_time', null)
                ->first();
        }

        $array = [
            'leadId' => $request->lead_id,
            'user' => $requestingUser,
            'repDetail' => $rep
        ];


        if ($line) {
            $line->filled_user_id = $user->id;
            $line->filled_time = Carbon::now()->toDateTime();
            $line->save();
            $payload = new LineResource($line);

            event(new NewQueueEvent($payload, 'filled'));
            event(new ElevatorEvent($line->type, -1));
        }

        $userHasLead = UserHasLead::where('lead_id', $rep->lead_id)
            ->where('position_id', $rep->position_id)
            ->where('user_id', $rep->user_id)
            ->with('user', 'position')
            ->first();
        $something = new LeadRepsResource($userHasLead);

        event(new RepAddedToLeadEvent($something, $rep->lead_id));
        $this->rep = $rep;
        $body = "You have been assigned a new customer. "
            . URL::to('/') . "/dashboard/lead/" . $request->lead_id;
//         event(new TextEvent($user->user->phone_number, $body));

        $lead = Lead::where('id', $request->lead_id)->with('customer')->first();

        $body = 'You have a lead with ' . $lead->customer->last_name .
            ' @' . $lead->customer->zip_code . '   ';
        $link = URL::to('/') . '/dashboard/lead/' . $lead->id;
        HelperController::email('New Lead (' . $request->lead_id . ')', $body, $link, $user, 'Lead');
// create  new UpdateZapierEvent
        event(new UpdateZapierEvent($lead, 'sit'));


        return $something;
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($leadId, $id)
    {

//        LeadsResource::collection($lead->leads);

        return LeadNotesResource::collection(LeadNote::where('lead_id', '=', $leadId)->orderBy('created_at', 'desc')->with('user')->get());

    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $leadId, $id)
    {
        $lead = Lead::find($leadId);

        if ($request->action === 'delete') {
            $result = UserHasLead::where('id', '=', $request->id)
                ->delete();
            event(new UpdateZapierEvent($lead));

            if ($result) {
                return 1;
            } else {
                return $request;
            }
        } else {
            return 'not deleting? Creat something';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $leadId)
    {
        return $request;

    }
}
