<?php

namespace App\Http\Controllers\Backend\VendorLinks;


use App\Http\Controllers\Controller;
use App\Models\VendorLink\Category;
use App\Models\VendorLink\Link;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CategoryController extends Controller
{

	public function __construct()
	{
		$this->middleware(['role_or_permissions:administrator|executive|administrate all vendorlinks'])->only(['edit', 'create', 'store', 'update', 'destroy']);

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = Category::all();
		return view('backend.VendorLinks.category.index', compact('categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('backend.VendorLinks.category.create');
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
			'name' => 'required|unique:categories,name|max:100',
			'sort_order' => 'required|unique:categories,name,NULL,id,deleted_at,NULL',
			'description' => 'required'
		]);

		$category = new Category();

		$category->name        = ucfirst($request['name']);
		$category->description = $request['description'];
		$category->sort_order  = $request['sort_order'];
		$category->save();

		return redirect('dashboard/partnerlinks/category')->with('success', 'Category Created');

	}

	/**
	 * Display the specified resource.
	 *
	 * @param Category $category
	 * @return Response
	 */
	public function show(Category $category)
	{
		return view('backend.VendorLinks.category.show', compact('category'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Category $categories
	 * @return Response
	 */
	public function edit(Category $category)
	{
		return view('backend.VendorLinks.category.edit', compact('category'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param Category
	 * @return Response
	 */
	public function update(Request $request, Category $category)
	{
		$validator = $request->validate([
			'name' => 'required|unique:categories,name,' . $category->id . '|max:100',
			'sort_order' => 'required|unique:categories,name,' . $category->id . ',id,deleted_at,NULL',
		]);
//        return $request;
		$update              = Category::find($category->id);
		$update->sort_order  = $request->sort_order;
		$update->name        = $request->name;
		$update->description = $request->description;
		$update->save();

		return redirect(route(('dashboard.vendorlinks.category.index')))->with('success', 'Category Updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Category $categories
	 * @return Response
	 * @throws Exception
	 */
	public function destroy(Request $request, Category $category)
	{
		Link::where('category_id', '=', $category->id)->delete();
		$category->delete();


		return redirect(route(('dashboard.vendorlinks.category.index')))->with('success', 'Category Deleted');
	}
}
