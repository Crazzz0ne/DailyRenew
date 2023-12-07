<?php


namespace App\Http\Controllers\Api\SalesFlow\Queue;


use App\Events\Backend\SalesFlow\Lead\ProposedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\RequestedSystemEvent;
use App\Events\Backend\SalesFlow\Lead\SalesRabbit\CreateSalesRabbitLeadEvent;
use App\Events\Backend\SalesFlow\Queue\ElevatorEvent;
use App\Events\Backend\SalesFlow\Queue\NewQueueEvent;
use App\Events\Backend\SalesFlow\Queue\QueuePageEvent;
use App\Events\Backend\SalesFlow\RepAddedToLeadEvent;
use App\Events\Backend\SalesFlow\TextEvent;
use App\Events\Backend\SalesFlow\Users\Notifications\NewUserNotificationEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Resources\Lead\LineResource;
use App\Http\Resources\LeadRepsResource;
use App\Mail\SalesFlow\BaseMailable;
use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\Appointment;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Line;
use App\Models\SalesFlow\Lead\System\ProposedSystem;
use App\Models\SalesFlow\Lead\System\RequestedSystem;
use App\Models\SalesFlow\Lead\UserHasLead;
use App\Notifications\Slack\SlackService;
use App\Repositories\Backend\SalesFlow\Lead\LeadRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Slack;

class LineController extends Controller
{
    protected $leadRepository;

    public function __construct(lead $lead)
    {
        $this->leadRepository = new LeadRepository();
    }

    public function leadLine(Request $request, Lead $lead)
    {
        return LineResource::collection(Line::where('lead_id', $lead->id)->with('filledUser')->get());
    }

//    time to fill for urgent
    public function timeToFill()
    {

// Convert the user's timezone to UTC before performing calculations.
        $userTimezone = 'America/Los_Angeles';

// Convert the user's timezone to UTC before performing calculations.
           $todayAvg = $this->calculateAverage('filled_time', null, 1, Carbon::today($userTimezone)->startOfDay(), Carbon::today($userTimezone)->endOfDay(), $userTimezone);

        $sevenDayAvg = $this->calculateAverage('filled_time', null, 1, Carbon::now($userTimezone)->subDays(7)->startOfDay(), null, $userTimezone);
        $yesterdayAvg = $this->calculateAverage('filled_time', null, 1, Carbon::yesterday($userTimezone)->startOfDay(), Carbon::yesterday($userTimezone)->endOfDay(), $userTimezone);

        return [
            'sevenDayAvg' => round($sevenDayAvg, 2),
            'yesterday' =>  round($yesterdayAvg, 2),
            'today' =>  round($todayAvg, 2)
        ];


    }

    function calculateAverage($field, $value, $urgent, $startDate, $endDate = null, $timezone = 'UTC') {
        $query = Line::where($field, '!=', $value)
            ->where('type', 'build_proposal')
            ->where('urgent', $urgent)
            ->where('created_at', '>', $startDate->setTimezone('UTC'));

        if ($endDate) {
            $query->where('created_at', '>=', $startDate->setTimezone('UTC'))
                ->where('created_at', '<', $endDate->setTimezone('UTC'));
        }

        $lines = $query->get();

        if ($lines->isEmpty()) {
            return 0;
        }

        $total = 0;
        foreach ($lines as $line) {
            $createdAtUserTz = Carbon::parse($line->created_at);

            // Use the correct end of the day for yesterday and start of the day for today.
//            if ($endDate && $createdAtUserTz >= $endDate) {
//                $createdAtUserTz = $endDate;
//            } elseif (!$endDate && $createdAtUserTz > Carbon::now($timezone)) {
//                $createdAtUserTz = Carbon::now($timezone);
//            }

            $total += $createdAtUserTz->diffInMinutes($line->filled_time);
        }

        return $total / $lines->count();
    }

    public function setGoBack(Request $request)
    {

        $apiKey = substr($request->header('Authorization'), -80);
        $user = User::where('api_token', $apiKey)
            ->first();
        $customer = Lead::where('id', $request->lead_id)->with('customer')->first();

        $appointment = new Appointment();
        $appointment->start_time = Carbon::parse($request->start)->toDateTimeString();
        $appointment->finish_time = Carbon::parse($request->start)->addMinutes(30)->toDateTimeString();
        $appointment->subject = 'Go Back @ ' . $customer->customer->full_name;
        $appointment->type_id = 9;
        $appointment->user_id = $user->id;
        $appointment->lead_id = $request->lead_id;
        $appointment->save();

        $queue = new Line();
        $queue->requested_user_id = $user->id;
        $queue->lead_id = $request->lead_id;
        $queue->type = 'Go Back';
        $queue->save();

    }

    public function queueCounts(Request $request)
    {
        $apiKey = substr($request->header('Authorization'), -80);
        $user = User::where('api_token', $apiKey)
            ->first();

        switch ($request->type === 'all') {
            case 'all':
                $totalInQueue = Line::where('filled_time', null)->get()->count();
                $sp1 = Line::where('filled_time', null)
                    ->whereHas('lead', function ($q) use ($user) {
                        $q->where('office_id', $user->office_id);
                    })
                    ->where('type', 'sp1')
                    ->get()
                    ->count();

                $integrations = Line::where('filled_time', null)
                    ->where('type', 'integrations')
                    ->get()
                    ->count();

                $creditCheck = Line::where('filled_time', null)
                    ->where('type', 'credit app')
                    ->get()
                    ->count();

                $proposal = Line::where('filled_time', null)
                    ->where('type', 'proposal builder')
                    ->get()
                    ->count();

                $sp1WaitTime = Line::where('filled_time', '!=', null)
                    ->where('type', 'sp1')
                    ->where('created_at', '>', Carbon::today()->toDateTimeString())
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get()
                    ->pluck('filled_time', 'created_at');

                $integrationsWaitTime = Line::where('filled_time', '!=', null)
                    ->where('type', 'integrations')
                    ->where('created_at', '>', Carbon::today()->toDateTimeString())
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get()
                    ->pluck('filled_time', 'created_at');

                $creditAppWaitTime = Line::where('filled_time', '!=', null)
                    ->where('type', 'credit app')
                    ->where('created_at', '>', Carbon::today()->toDateTimeString())
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get()
                    ->pluck('filled_time', 'created_at');

                $proposalBuilderWaitTime = Line::where('filled_time', '!=', null)
                    ->where('type', 'proposal builder')
                    ->where('created_at', '>', Carbon::today()->toDateTimeString())
                    ->orderBy('id', 'desc')
                    ->limit(5)
                    ->get()
                    ->pluck('filled_time', 'created_at');


                $intavg = HelperController::averageWaitTime($integrationsWaitTime);
                $sp1avg = HelperController::averageWaitTime($sp1WaitTime);
                $creditAppAvg = HelperController::averageWaitTime($creditAppWaitTime);
                $proposalBuilderAvg = HelperController::averageWaitTime($proposalBuilderWaitTime);

                $all = [
                    'totalInQueue' => $totalInQueue,
                    'sp1' => [
                        'count' => $sp1,
                        'waitTime' => gmdate('i:s', $sp1avg)
                    ],
                    'integrations' => [
                        'count' => $integrations,
                        'waitTime' => gmdate('i:s', $intavg)
                    ],
                    'creditCheck' => [
                        'count' => $creditCheck,
                        'waitTime' => gmdate('i:s', $creditAppAvg)
                    ],
                    'proposal' => [
                        'count' => $proposal,
                        'waitTime' => gmdate('i:s', $proposalBuilderAvg)
                    ]
                ];
                return collect($all);
            case 'sp1':
                $sp1 = Line::where('filled_time', null)
                    ->where('type', 'sp1')
                    ->get()
                    ->count();
                return collect($sp1);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $line = Line::query();

        if ($request->type) {
            $line->where('type', $request->type);
            if ($request->lead_id) {
                $line->where('lead_id', $request->lead_id);
            }
        } else {
            if ($user->can('administrate company')) {
                $line->where('filled_time', null);
            } else {
                $line->where(function ($query) use ($user) {
                    $this->addUserPermissions($user, $query);
                });
            }
        }

        $line->whereNull('filled_time');

        $lineResult = $line->get();
        $lineResult->load('requestingUser', 'lead');

        return LineResource::collection($lineResult);
    }

    private function addUserPermissions($user, $query)
    {
        if ($user->can('accept sp1')) {
            $officeId = $user->office_id;
            $query->orWhere(function ($query) use ($officeId) {
                $query->where('type', 'sp1')
                    ->whereHas('lead', function ($query) use ($officeId) {
                        $query->where('office_id', $officeId);
                    });
            });
        }

        if ($user->can('accept proposal builder')) {
            $query->orWhere(function ($query) {
                $query->where('type', 'build_proposal')
                    ->orWhere('type', 'credit_app')
                    ->orWhere('type', 'sun_run_runner');
            });
        }

        $this->addUserPermissionsByType($user, $query, [
            'edit NTS' => 'NTS',
            'accept change order' => 'change_order',
            'accept roof assessor' => 'roof',
            'accept d2d call center' => 'd2d_call_center',
        ]);
    }

    private function addUserPermissionsByType($user, $query, array $permissions)
    {
        foreach ($permissions as $permission => $type) {
            if ($user->can($permission)) {
                $query->orWhere('type', $type);
            }
        }
    }

    public function store(Request $request)
    {
        $user = \Auth::user();

        switch ($request->type) {

            case 'integration':
                $lineCreated = Line::where('lead_id', $request->leadId)
                    ->where('type', 'integrations')
                    ->get();
                if ($lineCreated) {
                    $line = new Line();
                    $line->requested_user_id = $user->id;
                    $line->lead_id = $request->leadId;
                    $line->type = 'integrations';
                    $line->save();
//                    $this->leadRepository->requestTextIntegrations($user, $line->id);
                } else {
                    return $lineCreated;
                }
                break;
            case 'sp1':
                $lineCreated = Line::where('lead_id', $request->leadId)
                    ->where('filled_time', null)
                    ->where('type', 'sp1')
                    ->get();
                $lead = Lead::where('id', $request->leadId)->first();
                if (!$lineCreated->count()) {
                    $line = new Line();
                    $line->requested_user_id = $user->id;
                    $line->lead_id = $request->leadId;
                    $line->type = 'sp1';
                    $line->save();
                    event(new CreateSalesRabbitLeadEvent($lead, $user));
                } else {
                    return new LineResource($lineCreated->first());
                }
                break;
            case 'credit_app':
                $lineCreated = Line::where('lead_id', $request->leadId)
                    ->where('type', 'credit_app')
                    ->get();
                if ($lineCreated->count() == 0) {
                    $line = new Line();
                    $line->requested_user_id = $user->id;
                    $line->lead_id = $request->leadId;
                    $line->type = 'credit_app';
                    $line->save();

                    $options = [
                        'color' => 'good',
                        'fields' => [
                            [
                                'title' => 'New Credit Check',
                                'value' => "{$line->requestingUser->name} has just requested a credit app for {$line->lead->customer->name}!",
                                'short' => false,
                            ]
                        ],
                    ];

                    Slack::compose(Slack::link(config('app.url') . '/dashboard/lead/queue', 'View Queue'), config('slack.channels.proposal_builders'), $options);

                } else {
                    return new LineResource($lineCreated->first());
                }
                break;
            case 'sun_run_runner':
                $lineCreated = Line::where('lead_id', $request->leadId)
                    ->where('type', 'sun_run_runner')
                    ->get();
                if ($lineCreated->count() == 0) {

                    $line = new Line();
                    $line->requested_user_id = $user->id;
                    $line->lead_id = $request->leadId;
                    $line->type = 'sun_run_runner';
                    $line->save();


                    $proposedSystem = ProposedSystem::where('id', $request->typeId)->first();

                    RequestedSystem::where('lead_id', $request->leadId)
                        ->where('id', '!=', $proposedSystem->requested_system_id)
                        ->delete();
                    ProposedSystem::where('lead_id', $request->leadId)
                        ->where('id', '!=', $request->typeId)
                        ->delete();

                    $options = [
                        'color' => 'good',
                        'fields' => [
                            [
                                'title' => 'Request For sends Docs To customer',
                                'value' => "{$line->requestingUser->name} has just requested for documents to be sent for {$line->lead->customer->name}!",
                                'short' => false,
                            ]
                        ],
                    ];

                    Slack::compose(Slack::link(config('app.url') . '/dashboard/lead/queue', 'View Queue'), config('slack.channels.proposal_builders'), $options);
                } else {
                    return new LineResource($lineCreated->first());
                }
                break;
            case 'build_proposal':

                $line = new Line();
                $line->requested_user_id = $user->id;
                $line->lead_id = $request->leadId;
                $line->type = 'build_proposal';
                $line->save();

                $options = [
                    'color' => 'good',
                    'fields' => [
                        [
                            'title' => 'New Proposal Request!',
                            'value' => "{$line->requestingUser->name} has just requested a build proposal for {$line->lead->customer->name}!",
                            'short' => false,
                        ]
                    ],
                ];

                Slack::compose(Slack::link(config('app.url') . '/dashboard/lead/queue', 'View Queue'), config('slack.channels.proposal_builders'), $options);

                break;
            case 'sp2':
                $line = new Line();
                $line->requested_user_id = $user->id;
                $line->lead_id = $request->leadId;
                $line->type = 'sp2';
                $line->save();
                $sp2s = User::permission('accept sp2')->where('terminated', null)->where('office_id', $user->office_id)->get();
                $body = "In house sp1 needs back up.
                " . URL::to('/') . "/dashboard/lead/queue";
                foreach ($sp2s as $sp2) {
                    if ($sp2->phone_number) {
                        event(new TextEvent($sp2->phone_number, $body));
                    }
                }
                break;
            case 'd2d_call_center':
                $lineCreated = Line::where('lead_id', $request->leadId)
                    ->where('type', 'd2d_call_center')
                    ->get();
                if ($lineCreated->count() == 0) {
                    $line = new Line();
                    $line->requested_user_id = $user->id;
                    $line->lead_id = $request->leadId;
                    $line->type = 'd2d_call_center';
                    $line->save();

                } else {
                    return new LineResource($lineCreated->first());
                }
                break;
            case 'roof':
                $lineCreated = Line::where('lead_id', $request->leadId)
                    ->where('type', 'roof')
                    ->get();
                if ($lineCreated->count() == 0) {
                    $line = new Line();
                    $line->requested_user_id = $user->id;
                    $line->lead_id = $request->leadId;
                    $line->type = 'roof';
                    $line->save();

                    $options = [
                        'color' => 'good',
                        'fields' => [
                            [
                                'title' => 'New Roof Request!',
                                'value' => "{$line->requestingUser->name} has just place a roof accessor request for {$line->lead->customer->name}!",
                                'short' => false,
                            ]
                        ],
                    ];

                    Slack::compose(Slack::link(config('app.url') . '/dashboard/lead/queue', 'View Queue'), config('slack.channels.core'), $options);

                } else {
                    return new LineResource($lineCreated->first());
                }
                break;

        }

        $something = Line::where('id', $line->id)->with('requestingUser', 'filledUser')->first();
        $somethingElse = new LineResource($something);
        event(new NewUserNotificationEvent($somethingElse));
        event(new QueuePageEvent($somethingElse, 'assigned', null));
        event(new ElevatorEvent($line->type, 1));

        return $somethingElse;
    }

    public function show(Line $line, Request $request)
    {
        $apiKey = substr($request->header('Authorization'), -80);
        $user = User::where('api_token', $apiKey)
            ->get()
            ->first();
        if ($request->type === 'related') {
            $payload = Line::where('lead_id', $line->lead_id)->with('related', 'filledUser')->get();
            return LineResource::collection($payload);

        } else {
            $payload = Line::where('id', $line->id)->with('related', 'requestingUser', 'filledUser')->get()->first();
            return new LineResource($payload);
        }


    }

    public function history(Request $request)
    {

        $user = \Auth::user();

        $line = Line::query();

        $line->where('filled_time', '!=', null);

        if (!$user->hasAnyRole(['administrator', 'executive', 'proposal builder'])) {
            $line->where(function ($q) {
                $q->where('type', '!=', 'build_proposal');
                $q->Where('type', '!=', 'NTS');
            });
        }

        if (!$user->hasAnyRole(['administrator', 'executive', 'integrations', 'proposal builder'])) {
            $line->whereHas('lead', function ($q) use ($user) {
                $q->where('office_id', $user->office_id);
            });
        }

        $line->with('requestingUser', 'lead.customer', 'lead.office', 'filledUser');
        return LineResource::collection($line->orderBy('id', 'desc')->paginate(20));
    }

    public function update(Line $line, Request $request)
    {
        $user = \Auth::user();
        $notifications = $user->notifications;
        foreach ($notifications as $notification) {
            if (isset($notification->data['payload'])) {
                if ($notification->data['payload']['id'] === $line->id) {
                    $notification->batchDelete();
                }
            }
        }

        $payload = $this->assignUser($line, $user, $request->location);

        return $payload;
    }

    public function assignUser($line, $user, $location)
    {
        switch ($line->type) {
            case 'integrations':
                $positionId = 4;
                break;
            case 'sp1':
                $positionId = 2;
                break;
            case 'sp1 panic':
                $positionId = 2;
                break;
            case 'sp2':
                $positionId = 3;
                break;
            case 'credit_app':
                $positionId = 7;
                break;
            case 'build_proposal':
                $positionId = 6;
                break;
            case 'NTS':
                $positionId = 9;
                break;
            case 'send_paperwork':
                $positionId = 6;
                break;
            case 'Go Back':
                $positionId = 2;
                break;
            case 'Call_Center_Appointment':
                $positionId = 3;
                break;
            case 'sun_run_runner':
                $positionId = 8;
                break;
            case 'd2d_call_center':
                $positionId = 2;
                Lead::where('id', $line->lead_id)->update(['office_id' => $user->office_id]);
                break;
            case 'roof':
                $positionId = 10;
                break;
            case 'change_order':
                $positionId = 6;
                break;
            default:
                \Log::critical('Looks like the Assigned user id did not get set. Its going to 500!');
                break;
        }

        if ($line->filled_time === null) {
            $line->filled_user_id = $user->id;
            $line->filled_time = Carbon::now()->toDateTime();
            $line->save();

            $newUser = new UserHasLead();
            $newUser->user_id = $user->id;
            $newUser->lead_id = $line->lead->id;
            $newUser->position_id = $positionId;
            $newUser->save();
            $travelTime = null;

            $customer = Lead::where('id', $line->lead_id)
                ->with('customer')
                ->get()
                ->pluck('customer')
                ->first();
            //Gives Location to let requester know how far out.
            if ($line->type === 'sp2' || $line->type === 'sp1') {
                $lead = Lead::where('id', $line->lead_id)->with('customer')
                    ->first();

                $destination = $this->leadRepository->formatAddress($customer);
                try {
                    $travelTime = $this->leadRepository->travelTime($location['lat'] . ',' . $location['long'], $destination);
                } catch (Exception $exception) {
                    \Log::critical('location failed');
                }


                $body = $user->first_name . ' is on their way to see your customer ' . $lead->customer->first_name . '. They are ' . $travelTime . ' out.';

                $reps = User::find($line->requested_user_id);
                if ($reps->phone_number) {
                    event(new TextEvent($reps->phone_number, $body));
                }

            }
            //Moved out of the try because both was undefined.

            if ($line->type === 'credit_app') {
                $reps = User::find($line->requested_user_id);
                $body = 'We are creating your credit app hold tight.';
                if ($reps->phone_number) {
                    event(new TextEvent($reps->phone_number, $body));
                }
            }

            if ($line->type === 'Call_Center_Appointment') {

                $lead = Lead::where('id', $line->lead->id)->with('customer')->first();

                $lead->office_id = $user->office_id;
                $lead->save();

                $subject = 'Close ' . $user->first_name . ' ' . mb_substr($user->last_name, 0, 1) . '.  @'
                    . $lead->customer->full_name;

                Appointment::where('lead_id', $line->lead->id)
                    ->where('type_id', 6)
                    ->update(
                        [
                            'user_id' => $user->id,
                            'subject' => $subject
                        ]
                    );
            }

            if ($positionId === 3) {
                $appointment = Appointment::where('lead_id', $line->lead_id)->where('type_id', 6)->first();

                $body = 'Congratulations your assigned to ' . $customer->first_name . ' ' . $customer->first_name
                    . ' @ ' . Carbon::parse($appointment->start_time)->format('j F, Y g:i a') . '.  👏';
                event(new TextEvent($user->phone_number, $body));
            }


            $temp = Line::where('id', $line->id)->with('filledUser')->first();

            $payload = new LineResource($temp);
            try {
                $lineUser = UserHasLead::where('lead_id', $line->lead_id)
                    ->where('position_id', $positionId)
                    ->with('user', 'position')
                    ->first();
                $something = new LeadRepsResource($lineUser);
                event(new RepAddedToLeadEvent($something, $line->lead_id));
                event(new NewQueueEvent($payload, 'filled', $travelTime));
                event(new QueuePageEvent($payload, 'filled', null));

//                event(new QueueTakenEvent($payload));
                event(new ElevatorEvent($line->type, -1));
            } catch (Exception $e) {
                \Log::critical($e);
            }


            if ($positionId === 4) {
                if ($line->type == 'integrations') {
                    $body = $user->full_name . ' is ready to take your call ' . $user->phone_number;
                }

                $canvassersNumber = User::find($line->requested_user_id);

                if ($canvassersNumber->phone_number) {
                    event(new TextEvent($canvassersNumber->phone_number, $body));
                }
            }


            $data = [
                'ok' => true,
            ];

            return collect($data);

        } elseif ($user->id === $line->filled_user_id) {


            $data = [
                'ok' => true,
                'progress' => true,
            ];

        } else {
            $filledUser = User::where('id', $line->filled_user_id)->first();

            $data = [
                'ok' => false,
                'taken_name' => $filledUser->full_name
            ];

        }

        collect($data);
        return $data;
    }

    public function textAlert($body, $officeId)
    {

        $sp1s = User::whereHas("positions", function ($q) {
            $q->where("position_id", '2');
        })->get();

        foreach ($sp1s as $sp1) {
            if ($sp1->office_id == $officeId && $sp1->phone_number != null) {

                event(new TextEvent($sp1->phone_number, $body));
                \Log::info('SP1 text was sent ' . $sp1->phone_number);

            }
        }

        return 'Sms Sent';
    }

    public function sp1ListByOffice($officeId)
    {

        $sp1s = User::whereHas("positions", function ($q) {
            $q->where("position_id", '2');
        })->get();
        $array = [];
        foreach ($sp1s as $sp1) {
            if ($sp1->office_id == $officeId && $sp1->phone_number != null) {
                array_push($array, $sp1->phone_number);
            }
        }
        return $array;
    }

    public function proposalBuilderEmails()
    {

        $builders = User::whereHas("positions", function ($q) {
            $q->where("position_id", '6');
        })->get();
        $array = [];
        foreach ($builders as $builder) {
            if ($builder->phone_number) {
                array_push($array, $builder->email);
            }
        }
        return $array;
    }

    public function email($subject, $body, $link, $rep)
    {
        Mail::to($rep->email)->queue(new BaseMailable($subject, $body, $link, 'lead'));

        return 'Sms Sent';
    }

    public function position(Line $line, Request $request)
    {

        return $total = Line::where('type', $line->type)
            ->where('filled_user_id', null)
            ->where('created_at', '<', Carbon::parse($line->created_at)->toDateTimeString())
            ->count();


    }

    public function destroy(Line $line, Request $request)
    {

        if ($line->type === 'sun_run_runner') {
            $requests = RequestedSystem::withTrashed()->where('lead_id', $line->lead_id)->where('deleted_at', '!=', null)->get();
            $proposals = ProposedSystem::withTrashed()->where('lead_id', $line->lead_id)->where('deleted_at', '!=', null)->get();

            ProposedSystem::withTrashed()->where('lead_id', $line->lead_id)
                ->restore();
            RequestedSystem::withTrashed()->where('lead_id', $line->lead_id)
                ->restore();

            foreach ($proposals as $proposal) {
                event(new ProposedSystemEvent($proposal, $line->lead_id, true));
            }
            foreach ($requests as $request) {
                event(new RequestedSystemEvent($request, $line->lead_id, true));
            }

        }
        if ($line->type === 'd2d_call_center') {
            UserHasLead::where('lead_id', $line->lead_id)->where('position_id', 2)->delete();
            $lead = Lead::where('id', $line->lead_id)->first();
            $lead->office_id = $lead->origin_office_id;
            $lead->save();
        }


        return $line->delete();


    }
}
