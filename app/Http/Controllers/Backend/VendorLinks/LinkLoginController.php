<?php


namespace App\Http\Controllers\Backend\VendorLinks;


use App\Models\VendorLink\LinkLogin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LinkLoginController
{
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function create(Request $request)
	{
		$linkId = $request->linkId;
		return view('backend.VendorLinks.link.login.create', compact('linkId'));
	}

	public function store(Request $request)
	{

		$login            = new LinkLogin();
		$login->user_name = $request->user_name;
		$login->password  = $request->password;
		$login->link_id   = $request->linkId;

		if ($login->save()) {
			return redirect()->route('dashboard.vendorlinks.link.show', ['link' => $login->link_id]);
		} else {
			return redirect()->back()->withErrors('Something went wrong');
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param LinkLogin $linkLogin
	 * @return Response
	 */

	public function edit($linkLogin)
	{
//        return $linkLogin;
		$login = LinkLogin::where('id', '=', $linkLogin)->get()->first();

		if (isset($login)) {
			return view('backend.VendorLinks.link.login.edit', compact('login'));
		} else {
			return redirect()->back()->withErrors('Something went wrong');
		}
	}

	public function update(Request $request)
	{
		$login            = LinkLogin::find($request->linkId);
		$login->user_name = $request->user_name;
		$login->password  = $request->password;

		if ($login->save()) {
			return redirect()->route('dashboard.vendorlinks.link.show', ['link' => $login->link_id]);
		} else {
			return redirect()->back()->withErrors('Something went wrong');
		}
	}

	public function destroy($linkLogin)
	{
//        return $linkLogin;
		$deleteMe = LinkLogin::where('id', '=', $linkLogin)->get()->first();
		$deleteMe->delete();

		return redirect()->back()->withSucess('Something went right');
	}
}
