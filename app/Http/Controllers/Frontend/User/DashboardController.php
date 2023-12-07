<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Announcement\Announcement;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
	/**
	 * @return Factory|View
	 */
	public function index()
	{
		return $announcements = Announcement::all()->sortByDesc('id')->take(10);
		return view('frontend.user.dashboard', compact('announcements'));

	}

}
