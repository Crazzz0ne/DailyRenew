<?php

namespace App\Http\Controllers\Backend\VendorLinks;


use App\Http\Controllers\Controller;
use App\Models\VendorLink\Category;
use App\Models\VendorLink\Link;
use App\Models\VendorLink\LinkLogin;
use App\Models\VendorLink\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class LinkController extends Controller
{

	public function __construct()
	{
		$this->middleware(['role_or_permissions:administrator|executive|administrate all vendorlinks'])->only(['edit', 'create', 'store', 'update', 'destroy']);

	}

	public function index()
	{
		return view('backend.VendorLinks.link.index');
//        return Link::with('vendors', 'categories')->get();
//        return $link;
	}

	public function nextSort()
	{
		$sortArray     = [];
		$sortAvailable = [];
		$linkSort      = Link::select('sort_id', 'category_id')->get();

		foreach ($linkSort as $link) {
			$sortArray[$link->category_id][] = $link->sort_id;
		}

		foreach ($sortArray as $category => $array) {
			$x = 1;
			foreach ($array as $a) {
				if (!in_array($x, $array)) {
					$sortAvailable[$category][] = $x;
				}
				$x++;
			}
			$sortAvailable[$category][] = max($sortArray[$category]) + 1;
		}

		return $sortAvailable;
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		$vendors = Vendor::all()->where('id', '!=', 1);

		$categories = Category::all();
		$y          = 0;
//        $sortAvailable = $this->nextSort();
//        return $sortAvailable;

		return view('backend.VendorLinks.link.create', compact('vendors', 'categories'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{

		$link = new Link();

		$link->representative = $request['representative'];
		$link->email          = $request['email'];
		$link->office_phone   = $this->format_phone($request['office_phone']);
		$link->web_address    = $request->web_address;

//        return HelperController::removeHtml($request->webaddress);
		$link->notes       = $request->notes;
		$link->extension   = $request['extension'];
		$link->cell_phone  = $this->format_phone($request['cell_phone']);
		$link->vendor_id   = $request['vendor'];
		$link->category_id = $request['category'];
		$link->active      = 1;
		$link->save();

		return redirect('dashboard/partnerlinks');
	}

	public function format_phone($phone)
	{
		$phone = preg_replace("[^0-9]", "", $phone);

		if (strlen($phone) == 7)
			return preg_replace("/(\d{3})(\d{4})/", "$1-$2", $phone);
		elseif (strlen($phone) == 10)
			return preg_replace("/(\d{3})(\d{3})(\d{4})/", "$1-$2-$3", $phone);
		else
			return $phone;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Link $link
	 * @return Response
	 */
	public function show(Link $link)
	{
		$link = Link::where('id', '=', $link->id)->with('vendors', 'categories')->get()->first();

		$login = LinkLogin::where('link_id', '=', $link->id)->first();

//        return $login;

		return view('backend.VendorLinks.link.show', compact('link', 'login'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param Link $link
	 * @return Response
	 */
	public function edit(Link $link)
	{
		$vendors    = Vendor::all()->where('id', '!=', 1);
		$categories = Category::all();

		return view('backend.VendorLinks.link.edit', compact('vendors', 'categories', 'link'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param Link $link
	 * @return Response
	 */
	public function update(Request $request, Link $link)
	{

		$link->representative = $request->representative;
		$link->email          = $request->email;
		$link->web_address    = $request->web_address;
		$link->notes          = $request->notes;
		$link->office_phone   = $request->office_phone;
		$link->cell_phone     = $request->cell_phone;
		$link->vendor_id      = $request->vendor;
		$link->category_id    = $request->category;
		$link->update();

		return redirect('/dashboard/partnerlinks')->with('success', 'Link Updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Link $link
	 * @return Response
	 * @throws Exception
	 */
	public function destroy(Link $link)
	{
		$link->delete();

		return redirect('/dashboard/partnerlinks/')->with('success', 'Link Deleted');
	}
}
