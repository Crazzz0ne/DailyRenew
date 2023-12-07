<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class HomeController.
 */
class HomeController extends Controller
{
	/**
	 * @return View
	 */
	public function index()
	{
		if (auth()->check()) {
			if (auth()->user()->can('view backend')) {
				return redirect()->route('dashboard.dashboard');
			}

			return redirect()->route('dashboard.dashboard');
		}

		return view('frontend.index');
	}

}
