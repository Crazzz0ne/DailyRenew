<?php

namespace App\Http\Controllers\Backend\Announcement;

use App\Http\Controllers\Controller;
use App\Mail\Announcement\AnnouncementMailable;
use App\Models\Announcement\Announcement;
use App\Models\Announcement\AnnouncementHasUser;
use App\Models\Auth\User;
use App\Repositories\Backend\Admin\AnnouncementRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mail;

class AnnouncementController extends Controller
{
	protected $announcementRepository;

	/**
	 * UserController constructor.
	 *
	 * @param AnnouncementRepository $announcementRepository
	 */
	public function __construct(AnnouncementRepository $announcementRepository)
	{
		//
		$this->announcementRepository = $announcementRepository;

		$this->middleware(['role_or_permissions:administrator|executive|administrate all announcements'])->only(['edit', 'create', 'store', 'update', 'destroy']);

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$announcements = $this->announcementRepository->stripedHtml(Auth::user()->id, 20);

		return view('backend.announcement.index', compact('announcements'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('backend.announcement.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{




//		$validator = Validator::make($request->all(), [
//			'subject' => 'required|unique:announcements|max:255',
//			'body' => 'required',
//		]);
//
//		if ($validator->fails()) {
//			return redirect('dashboard/announcement/create')
//				->withErrors($validator)
//				->withInput();
//		}

//    	return $request;

		$subject = $request->subject;
		$body    = $request->body;
		$userId  = Auth::id();
		$color   = $request->color;
		$sticky  = $request->sticky;
//return $request->all();

		$announcement = new Announcement();
		$data = $request->all();
		$data['user_id'] = Auth::id();
		$announcementId = $announcement->create($data)->id;


//		$announcement->create(['subject' => request('content'),
//            'body' => request('announcement-trixFields'),
//
//            ]);

		$users = User::all();

		foreach ($users as $user) {
//            return $user->id;
			$tracking = new AnnouncementHasUser();
			$tracking->create(['user_id' => $user->id, 'announcement_id' => $announcementId]);
            Mail::to($user)->queue(new AnnouncementMailable($subject, $announcementId));


		}

//        new AnnouncementCreated($announcement);

        return redirect()->route('dashboard.dashboard')->withFlashSuccess('Announcement Updated!');



    }

	/**
	 * Display the specified resource.
	 *
	 * @param Announcement $announcement
	 * @return Response
	 */
	public function show(Announcement $announcement)
	{
//         User::with('userHasAnnouncement')->get();
		$user = Auth::user();
		AnnouncementHasUser::where([
			['user_id', '=', $user->id],
			['announcement_id', '=', $announcement->id]
		])->delete();
		return view('backend.announcement.show', compact('announcement'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Announcement $announcement
	 * @return Response
	 */
	public function edit(Announcement $announcement)
	{
		return view('backend.announcement.edit', compact('announcement'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param \App\Models\Announcement\Announcement $announcement
	 * @return Response
	 */
	public function update(Request $request, Announcement $announcement)
	{
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $announcementId = $announcement->create($data)->id;
//		$announcement->subject = $request->subject;
//		$announcement->body    = $request->body;
//		$announcement->color   = $request->color;
//		$announcement->sticky  = $request->sticky;
//		$announcement->save();

		return redirect()->route('dashboard.announcement.index')->withFlashSuccess(__('Announcement Updated!'));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Announcement $announcement
	 * @return Response
	 */
	public function destroy(Announcement $announcement)
	{
		AnnouncementHasUser::where('announcement_id', '=', $announcement->id)->delete();
		$announcement->delete();


		return redirect('dashboard/announcement/')->withFlashSuccess(__('Announcement Deleted!'));
	}

}
