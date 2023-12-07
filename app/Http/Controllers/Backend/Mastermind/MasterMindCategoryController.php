<?php

namespace App\Http\Controllers\Backend\Mastermind;


use App\Http\Controllers\Controller;
use App\Models\Mastermind\MastermindCategory;
use App\Models\Mastermind\MastermindContent;
use App\Repositories\Backend\Admin\TrainingContentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class MasterMindCategoryController extends Controller
{

	// TODO: I should be using something else then a repository for this... But this is faster....
	protected $trainingContentRepository;

	public function __construct(TrainingContentRepository $trainingContentRepository)
	{
		$this->trainingContentRepository = $trainingContentRepository;

		$this->middleware(['role_or_permissions:administrator|executive|administrate all masterminds'])->only(['edit', 'create', 'store', 'update', 'destroy']);
	}

	public function all()
	{
		$categories = MastermindCategory::all();
		return view('backend.mastermind.index', compact('categories'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		return view('backend.mastermind.category.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('backend.mastermind.category.create');
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
			'name' => 'required|unique:mastermind_categories,name|max:80',
		]);

		$category              = new MastermindCategory();
		$category->name        = $request->name;
		$category->description = $request->description;
		$category->save();

		return redirect('dashboard/mastermind')->with('success', 'A new category has been created');
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


		$contents = MastermindContent::where('category_id', '=', $id)
			->whereBetween('created_at', [$dt1, $dt2])
			->orderBy('created_at', 'desc')
			->with('tags')
			->get();

		$contents = $this->trainingContentRepository->stripedHtml($contents);
//        return $contents;
		return view('backend.mastermind.category.show', compact('contents', 'currentYear', 'lastYear'));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return view
	 */
	public function edit($id)
	{
		$category = MastermindCategory::where('id', '=', $id)->first();
		return view('backend.mastermind.category.edit', compact('category'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param MastermindCategory $category
	 * @return Response
	 */
	public function update(Request $request, MastermindCategory $category)
	{
		$category->name        = $request->name;
		$category->description = $request->description;
		$category->update();

		return redirect('dashboard/mastermind/')->with('success', 'Mastermind Category Updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param MastermindCategory $category
	 * @return Response
	 */
	public function destroy(MastermindCategory $category)
	{


		MastermindContent::where('category_id', $category->id)->delete();
		$category->delete();
		return redirect('dashboard/mastermind')->with('success', 'Category deleted with all associated content');
	}
}
