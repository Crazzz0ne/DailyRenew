<?php

namespace App\Http\Controllers\Backend\SalesFlow\Lead;

use App\Http\Controllers\Controller;

class LeadController extends Controller
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
