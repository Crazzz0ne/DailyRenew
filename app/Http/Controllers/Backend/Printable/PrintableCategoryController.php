<?php

namespace App\Http\Controllers\Backend\Printable;


use App\Http\Controllers\Controller;

use App\Http\Controllers\HelperController;
use App\Models\Collateral\CollateralCategory;
use App\Models\Collateral\CollateralContent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PrintableCategoryController extends Controller
{

	public function __construct()
	{
		$this->middleware(['role_or_permissions:administrator|executive|administrate all printables'])->only(['edit', 'create', 'store', 'update', 'destroy']);

	}

	public function all()
	{

		$categories = CollateralCategory::all();

		return view('backend.printable.index', compact('categories'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = CollateralCategory::all();

		return view('backend.printable.category.index', compact('categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('backend.printable.category.create');
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
			'name' => 'required|unique:collateral_categories,name|max:80',
		]);

		$category              = new CollateralCategory();
		$category->name        = $request->name;
		$category->description = $request->description;
		$category->save();


		return redirect('dashboard/printable')->with('success', 'A new category has been created');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return view
	 */
	public function show($id)
	{
		$contents = CollateralContent::where('category_id', '=', $id)
			->get();
		$contents = HelperController::stripedHtml($contents);
		return view('backend.printable.category.show', compact('contents'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param CollateralCategory $category
	 * @return Response
	 */
	public function edit(CollateralCategory $category)
	{

		return view('backend.printable.category.edit', compact('category'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param CollateralCategory $category
	 * @return Response
	 */
	public function update(Request $request, CollateralCategory $category)
	{
		$category->name        = $request->name;
		$category->description = $request->description;
		$category->update();

		return redirect('dashboard/printable')->with('success', 'Collateral Category Updated');

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param CollateralCategory $printableCategory
	 * @return Response
	 */
	public function destroy(CollateralCategory $printableCategory)
	{
		CollateralContent::where('id', $printableCategory->id)->delete();
		CollateralCategory::where('category_id', $printableCategory->id)->delete();
		return redirect('dashboard/printable')->with('success', 'Category deleted with all associated content');
	}
}
