<?php

namespace App\Http\Controllers\Backend\Printable;


use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Collateral\CollateralCategory;
use App\Models\Collateral\CollateralContent;
use App\Models\VendorLink\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PrintableContentController extends Controller
{


	/**
	 * @var CollateralContent
	 */
	private $collateralContent;

	public function __construct(CollateralContent $collateralContent)
	{
		$this->CollateralContent = $collateralContent;
		$this->middleware(['role_or_permissions:administrator|executive|administrate all printables'])->only(['edit', 'create', 'store', 'update', 'destroy']);

	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = CollateralCategory::all()->orderBy('id', 'desc');

		return view('backend.printable.content.index', compact('categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$categories = CollateralCategory::all();
		$states     = HelperController::states();
		$vendors    = Vendor::all();
		return view('backend.printable.content.create', compact('categories', 'states', 'vendors'));
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
			'name' => 'required|max:80',
			'pdf' => 'required|mimes:pdf|max:10000',
			'state' => 'required|max:3',
			'vendor_id' => 'required',
		]);

		$file = $request->file();
		//TODO Files are public...We need to authenticate people before they can view the files, possibly
		$path = Storage::disk('s3')->put('/collateral/pdf', $request->pdf, 'public');

		$content              = new CollateralContent();
		$content->name        = $request->name;
		$content->description = $request->description;
		$content->category_id = $request->category;
		$content->state       = $request->state;
		$content->vendor_id   = $request->vendor_id;
		$content->path        = $path;
		$content->user_id     = Auth::user()->id;
		$content->size        = $request->pdf->getClientSize();
		$content->save();

		return redirect('dashboard/printable/' . $request->category . '/view')->with('success', 'Collateral created');
	}

	/**
	 * Show the specified resource from storage.
	 *
	 * @param CollateralContent $content
	 * @return Response
	 */
	public function show(CollateralContent $content)
	{

		return view('backend.printable.content.show', compact('content'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param CollateralContent $content
	 * @return Response
	 */
	public function edit(CollateralContent $content)
	{

		$states     = HelperController::states();
		$vendors    = Vendor::all();
		$categories = CollateralCategory::all();
		return view('backend.printable.content.edit', compact('categories', 'content', 'states', 'vendors'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param CollateralContent $content
	 * @return Response
	 */
	public function update(Request $request, CollateralContent $content)
	{
		$validator = $request->validate([
			'name' => 'required|max:80',
			'pdf' => 'mimes:pdf|max:10000',
			'state' => 'required|max:3',
			'vendor_id' => 'required',

		]);

		$content->name        = $request->name;
		$content->description = $request->description;
		$content->category_id = $request->category;
		$content->state       = $request->state;
		$content->vendor_id   = $request->vendor_id;

		$categoryName = CollateralCategory::where('id', $request->category)->pluck('name')->first();

//        return $request->pdf;
		if ($request->pdf) {
			$path          = Storage::disk('s3')->put('/collateral/pdf', $request->pdf, 'public');
			$content->path = $path;
		}
		$content->update();
		return redirect('dashboard/printable/' . $request->category . '/view')->with('success', 'Collateral updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param CollateralContent $content
	 * @return Response
	 */
	public function destroy(CollateralContent $content)
	{
		$content->delete();
		//TODO: it does not delete the s3 file
//        return Storage::disk('s3')->delete($content->path);
		return redirect('dashboard/printable/' . $content->category_id . '/view')->with('success', 'Collateral Deleted');
	}
}
