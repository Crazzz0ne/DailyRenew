<?php

namespace App\Http\Controllers\Backend\VendorLinks;


use App\Http\Controllers\Controller;
use App\Models\VendorLink\Category;
use App\Models\VendorLink\Link;
use App\Models\VendorLink\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;


class VendorController extends Controller
{

	public function __construct()
	{
		$this->middleware(['role_or_permissions:administrator|executive|administrate all vendorlinks'])->only(['edit', 'create', 'store', 'update', 'destroy']);

	}

	public function index()
	{
		$vendors = Vendor::with('links')->get();
		return view('backend.VendorLinks.vendor.index', compact('vendors'));
	}

	public function all(Request $request)
	{
//	    isset($_GET['token']) && $_GET['token'] == '97A1F62159F8CC6A450C30DBBCF4DABEEBF7881AA5D52C5619E3C4A0DF6F8AE9'
		if (true) {
			$data             = [];
			$data['category'] = Category::with('links.vendors')->get();
//        foreach ($data['category'] as $cat){
//            return    asort($cat);
//        }


			$data['categoryNames'] = Category::all('id', 'name');
			$data['companyNames']  = Vendor::all('id', 'company_name')->where('id', '!=', 1);
			$data['tableHeaders']  = collect([
				['name' => 'link', 'sortable' => false, 'sortName' => 'vendors.id'],
				['name' => 'company', 'sortable' => false, 'sortName' => 'vendors.company_name'],
				['name' => 'rep', 'sortable' => false, 'sortName' => 'representative'],
				['name' => 'notes', 'sortable' => false, 'sortName' => 'notes'],
				['name' => 'email', 'sortable' => false, 'sortName' => 'email'],
				['name' => 'cell', 'sortable' => false, 'sortName' => 'cell_phone'],
				['name' => 'office', 'sortable' => false, 'sortName' => 'office_phone'],
			]);
			return $data;
		} else {
			return redirect('/');
		}
	}

	public function create()
	{
		return view('backend.VendorLinks.vendor.create');
	}

	public function store(Request $request)
	{
//        return $request;

		$input  = $request->web_address;
		$needle = 'https://';

		if (strpos($input, $needle) !== false) {
			return 'yes';
		}

		$validator   = $request->validate([
			'name' => 'required|unique:vendors,company_name|max:100',

		]);
		$logoAddress = null;
		// places logo in storage
		if ($request->logo) {

			$logoAddress = Storage::disk('s3')->put('partner/logo', $request->logo, 'public');
		} else {
			$logoAddress = 'solar-panel.png';
		}
		//Saves it
		$vendor               = new Vendor();
		$vendor->company_name = $request->name;
		$vendor->web_address  = $request->web_address;
		$vendor->picture      = $logoAddress;
		$vendor->save();

		return redirect('dashboard/partnerlinks/vendor');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Vendor $vendor
	 * @return Response
	 */

	public function show(Vendor $vendor)
	{
		$vendor->with('training');

		return view('backend.VendorLinks.vendor.show', compact('vendor'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * @param Vendor $vendor
	 * @return Response
	 */

	public function edit(Vendor $vendor)
	{
		return view('backend.VendorLinks.vendor.edit', compact('vendor'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param Vendor
	 * @return Response
	 */

	public function update(Request $request, Vendor $vendor)
	{
		$validator = $request->validate([
			'name' => 'required|unique:vendors,company_name,' . $vendor->id . '|max:100',

		]);

		$vendor->company_name = $request->name;


		if ($request->logo) {
			$logoAddress = Storage::disk('s3')->put('partner/logo', $request->logo, 'public');
//            $logoAddress = $request->logo->getClientOriginalName();
			$vendor->picture = $logoAddress;
		}
		$vendor->web_address = $request->web_address;
		$vendor->update();
		return redirect('dashboard/partnerlinks/vendor')->with('success', 'Vendor Updated');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Vendor $vendor
	 * @return Response
	 */
	public function destroy(Vendor $vendor)
	{
		Vendor::where('id', $vendor->id)->delete();
		Link::where('vendor_id', $vendor->id)->delete();
		return redirect('dashboard/partnerlinks/vendor')->with('success', 'Vendor Deleted with all associated links');
	}
}
