<?php

namespace App\Http\Controllers\Backend\Mastermind;



use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Mastermind\MastermindCategory;
use App\Models\Mastermind\MastermindContent;
use App\Models\VendorLink\Vendor;
use App\Repositories\Backend\Admin\TrainingContentRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MasterMindContentController extends Controller
{

	protected $trainingContentRepository;

	public function __construct(TrainingContentRepository $trainingContentRepository)
	{
		$this->trainingContentRepository = $trainingContentRepository;
		$this->middleware(['role_or_permissions:administrator|executive|administrate all masterminds'])->only(['edit', 'update', 'destroy']);

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = MastermindContent::all()->orderBy('id', 'desc');

		return view('backend.mastermind.content.index', compact('categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$categories = MastermindCategory::all();
		$states     = HelperController::states();
		$vendors    = Vendor::all();

		return view('backend.mastermind.content.create', compact('categories', 'vendors', 'states'));
	}

	public function tag()
	{
		if (isset($_GET['tag'])) {
			$tag = $_GET['tag'];
		} else {
			return redirect()->back();
		}

		$contents = MastermindContent::withAllTags([$tag])->with('tags')->get();
		$contents = $this->trainingContentRepository->stripedHtml($contents);

		return view('backend.mastermind.content.tag', compact('contents'));

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
//        return $request;

		//ToDo: not sure how to get this validator to work
//        $validator = $request->validate([
//            'name' => 'required|max:80',
//            'pdf' => 'required_without:youTube',
//            'youTube' => 'required_without:pdf'
//        ]);
		$validator = $request->validate([
			'name' => 'required|max:120',
//            'state' => 'required|max:3',
//            'vendor_id' => 'required',
//            'pdf' => 'mimes:pdf|max:10000',
		]);

		$content              = new MastermindContent();
		$content->name        = $request->name;
		$content->description = $request->description;
		$content->category_id = $request->category;
		$content->state       = $request->state;
		$content->vendor_id   = $request->vendor_id;
		$content->user_id     = Auth::user()->id;
		$content->approved    = $request->approved;
//        return $request->file;


		$categoryName = str_slug(MastermindCategory::where('id', $request->category)->pluck('name')->first(), '-');
		if ($request->pdf) {
			$path          = Storage::disk('s3')->put('mastermind/' . $categoryName . '/pdf', $request->pdf, 'public');
			$content->path = $path;
			$content->type = 'pdf';
		} elseif ($request->audio) {
			$path          = Storage::disk('s3')->put('mastermind/' . $categoryName . '/audio', $request->audio, 'public');
			$content->path = $path;
			$content->type = 'audio';
		} elseif ($request->youTube != '') {
			$content->path = HelperController::embedYoutube($request->youTube);
			$content->type = 'youTube';
		} else {
			$content->path = '';
			$content->type = 'none';
		}
		$content->user_id = Auth::user()->id;
		$content->save();

		if ($request->tags) {

			$tags = HelperController::addTags($request, 'Mastermind');
//            $tagstring = $request->tags;
//            $tags = explode(',', $tagstring);
//
//            $tagArray = [];
//            foreach ($tags as $tag) {
//                array_push($tagArray, ltrim($tag));
//            }
//
//            Tag::findOrCreate($tagArray, 'Mastermind');
			$content->attachTags($tags);

		}
		return redirect('dashboard/mastermind/' . $request->category . '/view')->with('success', 'Your submission has been received ' . Auth::user()->getFullNameAttribute());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return view
	 */
	public function show($id)
	{
		$content = MastermindContent::where('id', $id)->first();
		return view('backend.mastermind.content.show', compact('content'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param \App\Models\MasterMind\MastermindContent $content
	 * @return Response
	 */
	public function edit(MastermindContent $content)
	{
		$categories = MastermindCategory::all();
		$states     = HelperController::states();
		$vendors    = Vendor::all();
		return view('backend.mastermind.content.edit', compact('categories', 'content', 'vendors', 'states'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param \App\Models\MasterMind\MastermindContent $content
	 * @return Response
	 */
	public function update(Request $request, mastermindContent $content)
	{
//        return $request;
		$validator = $request->validate([
			'name' => 'required|max:80',
//            'state' => 'required|max:3',
//            'vendor_id' => 'required',
//            'pdf' => 'mimes:pdf|max:10000',

		]);

		$content->name        = $request->name;
		$content->description = $request->description;
		$content->category_id = $request->category;
		$content->state       = $request->state;
		$content->vendor_id   = $request->vendor_id;
		$content->approved    = $request->approved;
//        return $request->file('pdf');

		$categoryName = MastermindCategory::where('id', $request->category)->pluck('name')->first();
//        return $request;

		if ($request->pdf) {
			$path          = Storage::disk('s3')->put('mastermind/' . $categoryName . '/pdf', $request->pdf, 'public');
			$content->path = $path;
			$content->type = 'pdf';
		} elseif ($request->audio) {
			$path          = Storage::disk('s3')->put('mastermind/' . $categoryName . '/audio', $request->pdf, 'public');
			$content->path = $path;
			$content->type = 'audio';

		} elseif ($request->youTube) {
			$content->path = HelperController::embedYoutube($request->youTube);
			$content->type = 'youTube';
		}
		$tagstring = $request->tags;
		$tags      = explode(',', $tagstring);

		if ($request->tags) {
			$tags = HelperController::addTags($request, 'Training');
			$content->syncTags($tags);
		}

		$content->update();
		return redirect('dashboard/mastermind/' . $request->category . '/view')->with('success', 'Mastermind updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param \App\Models\MasterMind\MastermindContent $content
	 * @return Response
	 */
	public function destroy(MastermindContent $content)
	{
		MastermindContent::where('id', $content->id)->delete();
		return redirect('dashboard/mastermind/' . $content->category_id . '/view')->with('success', 'Mastermind Deleted');
	}
}
