<?php

namespace App\Http\Controllers\Backend\VendorLinks;


use App\Http\Controllers\Controller;

class VendorLinksController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth', ['except' => ['index', 'show']]);
	}

	public function index()
	{
		return view('backend.VendorLinks.index');
	}
}
