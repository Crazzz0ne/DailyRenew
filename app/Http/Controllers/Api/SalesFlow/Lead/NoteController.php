<?php


namespace App\Http\Controllers\Api\SalesFlow\Lead;


use App\Events\Backend\SalesFlow\Lead\Note\NewNoteEvent;
use App\Events\Backend\SalesFlow\LeadNewMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeadNotesResource;
use App\Http\Resources\Notes\LeadWithNotesResource;
use App\Http\Resources\Notes\NotesLeadsResource;
use App\Models\Office\Office;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadNote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return LeadNotesResource::collection(LeadNote::where('lead_id', '=', $request->lead_id)->get());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request ,
     * @return LeadNotesResource
     */
    public function store(Request $request)
    {
        $note = new LeadNote();
        $note->lead_id = $request->lead_id;
        $note->user_id = $request->user_id;
        $note->note = $request->note;
        $note->save();

        broadcast(new LeadNewMessageEvent($request->lead_id, $note->id, $request->note, $request->user_id, $request->urgent));
        event(new NewNoteEvent($request->lead_id, $note->id, $request->note, $request->user_id, $request->urgent));
        return new LeadNotesResource($note);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return AnonymousResourceCollection
     */
    public function show($leadId, $id)
    {
//        LeadsResource::collection($lead->leads);
        return LeadNotesResource::collection(LeadNote::where('lead_id', '=', $leadId)->orderBy('created_at', 'asc')->with('user')->get());
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
     * @return string
     */
    public function destroy($leadId, $noteId)
    {
        LeadNote::where('id', '=', $noteId)->delete();
        return 'success';
    }


    public function showAll(Request $request)
    {
        $user = \Auth::user();
        if ($user->hasAnyRole(['executive', 'administrator', 'proposal builder',])) {
//return LeadNote::where('id', '>', 10)->with('lead.customer')->orderBy('id', 'desc')->paginate(100);
            return LeadNotesResource::collection(LeadNote::where('id', '>', 10)->with('lead.customer')->orderBy('id', 'desc')->paginate(100));
        } else if ($user->hasAnyRole(['regional manager'])) {
            $regionalOfficeID = $user->office_id;
            $marketId = Office::where('id', $regionalOfficeID)->get()->pluck('market_id');
            $offices = Office::where('market_id', $marketId)->get()->pluck('id')->toarray();
            $notes = LeadNote::whereHas('lead', function ($q) use ($offices) {
                $q->whereIn('office_id', $offices);
            })->orderBy('id', 'desc')->with('lead.customer')->paginate(100);
            return LeadNotesResource::collection($notes);
        } elseif ($user->hasAnyRole(['manager'])) {
            $notes = LeadNote::whereHas('lead', function ($q) use ($user) {
                $q->hasOffice($user->office_id);
            })->orderBy('id', 'desc')->with('lead.customer')->paginate(100);
            return LeadNotesResource::collection($notes);

        } elseif ($user->hasAnyRole(['integrations'])) {
            $notes = LeadNote::whereHas('lead', function ($q) use ($user) {
                $q->hasOffice(10);
            })->orderBy('id', 'desc')->with('lead.customer')->paginate(100);
            return LeadNotesResource::collection($notes);
        } else {
            $notes = LeadNote::whereHas('lead', function ($q) use ($user) {
                $q->hasUser($user->id);
            })->orderBy('id', 'desc')->with('lead.customer')->paginate(100);
            return LeadNotesResource::collection($notes);
        }
//          ->paginate(100);

    }

    public function leads()
    {

        $user = \Auth::user();


        if ($user->hasAnyRole(['executive', 'administrator', 'proposal builder',])) {
            $notesLeadIds = LeadNote::query();
            $ids = [];
            if ($user->id == 17 || $user->id == 476){
                $notesLeadIds->whereHas('lead.office', function ($q) {
                    $q->where('market_id', '!=', 1);
                });

            }

            $notesLeadIds = $notesLeadIds->where('id', '>', 1)
                ->orderBy('id', 'desc')
                ->select('lead_id', 'created_at')
                ->limit(100)
                ->get();
        } else if ($user->hasAnyRole(['regional manager'])) {
            $regionalOfficeID = $user->office_id;
            $marketId = Office::where('id', $regionalOfficeID)->first();
            $offices = Office::where('market_id', $marketId->market_id)->get();
            $officeIds = $offices->pluck('id')->toarray();
            $notesLeadIds = LeadNote::whereHas('lead', function ($q) use ($officeIds) {
                $q->whereIn('office_id', $officeIds);
            })
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get();

        } elseif ($user->hasAnyRole(['manager'])) {

            $notesLeadIds = LeadNote::whereHas('lead', function ($q) use ($user) {
                $q->where('office_id', $user->office_id);
            })
                ->limit(100)
                ->orderBy('id', 'desc')
                ->get();

        } elseif ($user->hasAnyRole(['integrations'])) {
            $notesLeadIds = LeadNote::whereHas('lead', function ($q) use ($user) {
                $q->hasOffice(10);
            })->orderBy('id', 'desc')->with('lead.customer', 'user')
                ->limit(100)
                ->orderBy('id', 'desc')
                ->get();

        } else {
            $notesLeadIds = LeadNote::whereHas('lead', function ($q) use ($user) {
                $q->hasUser($user->id);
            })->orderBy('id', 'desc')
                ->with('lead.customer', 'user')
                ->limit(100)
                ->get();
        }


        $notesLeadIds = $notesLeadIds->pluck('lead_id');
        $uniqueIds = $notesLeadIds->unique();

        $uniqueIds->values()->all();

        $leads = Lead::whereIn('id', $uniqueIds)->with(['notes' => function ($q) {
            $q->orderBy('id', 'desc');

        }, 'customer', 'statusName', 'notes.user'])->get();

//        $grouped = $notes->groupBy('lead.id');
//        $grouped->all();
//        $payload = [];
//        $i = 0;


        return ['data' => LeadWithNotesResource::collection($leads), 'ids' => $uniqueIds];
    }

    public function leadMessage(Lead $lead)
    {

    $lead =    $lead->where('id', $lead->id)->with(['notes' => function ($q) {
            $q->orderBy('id', 'desc');
        }, 'customer', 'statusName', 'notes.user'])->first();

//        $grouped = $notes->groupBy('lead.id');
//        $grouped->all();
//        $payload = [];
//        $i = 0;


        return new  LeadWithNotesResource($lead);
    }

    public function latest(Lead $lead, Request $request)
    {

        return NotesLeadsResource::collection(LeadNote::where('lead_id', $lead->id)->where('id', '>', $request->maxId)->get());
    }
}
