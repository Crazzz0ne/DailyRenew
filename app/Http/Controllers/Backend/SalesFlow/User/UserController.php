<?php

namespace App\Http\Controllers\Backend\SalesFlow\User;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index()
	{
		return view('backend.lead.lead');
	}

}
