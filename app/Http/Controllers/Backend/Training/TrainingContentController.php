<?php

namespace App\Http\Controllers\Backend\Training;


use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Training\TrainingCategory;
use App\Models\Training\TrainingContent;
use App\Models\VendorLink\Vendor;
use App\Repositories\Backend\Admin\TrainingContentRepository;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrainingContentController extends Controller
{

	protected $trainingContentRepository;

	public function __construct(TrainingContentRepository $trainingContentRepository)
	{
		$this->trainingContentRepository = $trainingContentRepository;
		$this->middleware(['role_or_permissions:administrator|executive|administrate all trainings'])->only(['edit', 'create', 'store', 'update', 'destroy']);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = TrainingCategory::all()->orderBy('id', 'desc');

		return view('backend.training.content.index', compact('categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$categories = TrainingCategory::all();
		$states     = HelperController::states();
		$vendors    = Vendor::all();

		return view('backend.training.content.create', compact('categories', 'vendors', 'states'));
	}

	public function tag()
	{
		if (isset($_GET['tag'])) {
			$tag = $_GET['tag'];
		} else {
			return redirect()->back();
		}
		$contents = TrainingContent::withAnyTags([$tag])->with('tags')->get();
		$contents = $this->trainingContentRepository->stripedHtml($contents);

		return view('backend.training.content.tag', compact('contents'));

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
			'state' => 'required|max:3',
			'vendor_id' => 'required',
		]);

		$hasContent = false;

		if (!isset($request->pdf)) $hasContent = true;
		if (!isset($request->audio)) $hasContent = true;
		if (!isset($request->youTube)) $hasContent = true;
		if (!isset($request->video)) $hasContent = true;

		if ($hasContent == false) return back()->withError('Must Select Content to Upload');


		$content              = new TrainingContent();
		$content->name        = $request->name;
		$content->description = $request->description;
//        return $request->category;
		$content->category_id = $request->category;
		$content->state       = $request->state;
		$content->vendor_id   = $request->vendor_id;
		$content->user_id     = Auth::user()->id;
//        return $request->file;

//        return $request;
		$categoryName = str_slug(TrainingCategory::where('id', $request->category)->pluck('name')->first(), '-');
		if ($request->pdf) {
            $file = new File($request->pdf);

// Generate unique name with real extension using the same method used by Storage::putFile()
            $fileHash = str_replace('.' . $file->extension(), '', $file->hashName());
            $fileName = $fileHash . '.' . $request->file('pdf')->getClientOriginalExtension();


			$path = Storage::disk('s3')->putFileAs('training/' . $categoryName . '/pdf', $file, $fileName);
			$content->path = $path;
			$content->type = 'pdf';
		} elseif ($request->audio) {
            $file = new File($request->audio);

// Generate unique name with real extension using the same method used by Storage::putFile()
            $fileHash = str_replace('.' . $file->extension(), '', $file->hashName());
            $fileName = $fileHash . '.' . $request->file('audio')->getClientOriginalExtension();

			$path          = Storage::disk('s3')->putFileAs('training/' . $categoryName . '/audio', $file, $fileName, 'private');
			$content->path = $path;
			$content->type = 'audio';

		} elseif ($request->youTube) {
			$content->path = HelperController::embedYoutube($request->youTube);
			$content->type = 'youTube';
		} elseif ($request->video) {
            $file = new File($request->video);

// Generate unique name with real extension using the same method used by Storage::putFile()
            $fileHash = str_replace('.' . $file->extension(), '', $file->hashName());
            $fileName = $fileHash . '.' . $request->file('video')->getClientOriginalExtension();

			$path          = Storage::disk('s3')->putFileAs('training/' . $categoryName . '/video', $file, $fileName, 'private');
			$content->path = $path;
			$content->type = 'video';
		} else {
			$content->path = '';
			$content->type = '';
		}
		$content->user_id = Auth::user()->id;
		$content->save();

		if ($request->tags) {

			$tags = HelperController::addTags($request, 'Training');
			$content->attachTags($tags);

		}
		return redirect('dashboard/training/' . $request->category . '/view');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return view
	 */
	public function show($id)
	{
		$content = TrainingContent::where('id', $id)->first();



        $disk = Storage::disk('s3');


             $link = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), $content->path, Carbon::now()->addMinutes(30), []);

		return view('backend.training.content.show', compact('content', 'link'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param TrainingContent $content
	 * @return Response
	 */
	public function edit(TrainingContent $content)
	{
		$categories = TrainingCategory::all();
		$states     = HelperController::states();
		$vendors    = Vendor::all();
		$tag        = $content->tags()->get();


		$tags = [];
		for ($i = 0; $i < count($tag); $i++) {
			$tags[$i] = $tag[$i]->name;
		}

		$tags = array_map(function ($tag) {
			return ' ' . $tag;
		}, $tags);
		$tags = implode(",", $tags);


		return view('backend.training.content.edit', compact('categories', 'content', 'vendors', 'states', 'tags'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param TrainingContent $content
	 * @return Response
	 */
	public function update(Request $request, TrainingContent $content)
	{
//        return $content;
		$validator = $request->validate([
			'name' => 'required|max:80',
			'state' => 'required|max:3',
			'vendor_id' => 'required',
			'pdf' => 'mimes:pdf|max:10000',

		]);
//        return $content;
		$content->name        = $request->name;
		$content->description = $request->description;
		$content->category_id = $request->category;
		$content->state       = $request->state;
		$content->vendor_id   = $request->vendor_id;
//        return $request->file('pdf');

		$categoryName = TrainingCategory::where('id', $request->category)->pluck('name')->first();


		if ($request->pdf) {
			$path          = Storage::disk('s3')->put('training/' . $categoryName . '/pdf', $request->pdf, 'public');
			$content->path = $path;
			$content->type = 'pdf';
		} elseif ($request->audio) {
			$path          = Storage::disk('s3')->put('training/' . $categoryName . '/audio', $request->pdf, 'public');
			$content->path = $path;
			$content->type = 'audio';

		} elseif ($request->youTube) {
			$content->path = HelperController::embedYoutube($request->youTube);
			$content->type = 'youTube';
		} elseif ($request->video) {
			$path          = Storage::disk('s3')->put('training/' . $categoryName . '/video', $request->video, 'public');
			$content->path = $path;
			$content->type = 'video';
		}


		if ($request->tags) {
			// grab tags from request
//            $tagstring = $request->tags;
//            // breaks string into array, map to trim white space, filter out empties
//            $tags = explode(',', $tagstring);
//            $tags = array_map('trim', $tags);
//            $tags = array_filter($tags, function ($value) {
//                return !is_null($value) && $value !== '';
//            });
//            // if new tags make them
//            Tag::findOrCreate($tags, 'Training');

			$tags = HelperController::addTags($request, 'Training');
			// if not just sync them
			$content->syncTags($tags);
		}
		$content->update();
		return redirect('dashboard/training/' . $request->category . '/view')->with('success', 'Training updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param TrainingContent $content
	 * @return Response
	 */
	public function destroy(TrainingContent $content)
	{
		TrainingContent::where('id', $content->id)->delete();
		return redirect('dashboard/training/' . $content->category_id . '/view')->with('success', 'Training Deleted');
	}
}
