<?php

namespace App\Http\Controllers\Backend\Training;


use App\Http\Controllers\Controller;
use App\Models\Training\TrainingCategory;
use App\Models\Training\TrainingContent;
use App\Repositories\Backend\Admin\TrainingContentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrainingCategoryController extends Controller
{
	protected $trainingContentRepository;

	public function __construct(TrainingContentRepository $trainingContentRepository)
	{
		$this->trainingContentRepository = $trainingContentRepository;
		$this->middleware(['role_or_permissions:administrator|executive|administrate all trainings'])->only(['edit', 'create', 'store', 'update', 'destroy']);
	}

	public function all()
	{

//        $categories = TrainingCategory::all();

		return view('backend.training.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{


		return view('backend.training.category.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('backend.training.category.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$validator = $request->validate([
			'name' => 'required|unique:training_categories,name|max:80',
		]);

		$category              = new TrainingCategory();
		$category->name        = $request->name;
		$category->description = $request->description;
		$category->save();

		return redirect('dashboard/training')->with('success', 'A new category has been created');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return view
	 */
	public function show($id)
	{
		if (isset($_GET['year'])) {
			$currentYear = $_GET['year'];
		} else {
			$currentYear = now()->year;
		}

		$dt1      = Carbon::create($currentYear)->toDateTimeLocalString();
		$dt2      = Carbon::create($currentYear)->addMonth(12)->toDateTimeString();
		$lastYear = $currentYear - 1;
		$contents = TrainingContent::where('category_id', '=', $id)
			->whereBetween('created_at', [$dt1, $dt2])
			->orderBy('id', 'desc')
			->limit('25')
			->with('tags')
			->get();

		$contents = $this->trainingContentRepository->stripedHtml($contents);
//        return $contents;
		return view('backend.training.category.show', compact('contents', 'currentYear', 'lastYear'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return view
	 */
	public function edit($id)
	{
		$category = TrainingCategory::where('id', '=', $id)->first();
		return view('backend.training.category.edit', compact('category'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param TrainingCategory $category
	 * @return Response
	 */
	public function update(Request $request, TrainingCategory $category)
	{
		$category->name        = $request->name;
		$category->description = $request->description;
		$category->update();

		return redirect('dashboard/training/')->with('success', 'Training Category Updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param TrainingCategory $category
	 * @return Response
	 */
	public function destroy(TrainingCategory $category)
	{
		TrainingContent::where('category_id', $category->id)->delete();
		TrainingCategory::where('id', $category->id)->delete();

		return redirect('dashboard/training')->with('success', 'Category Deleted with all associated content');
	}
}
