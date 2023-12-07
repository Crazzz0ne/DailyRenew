<?php

namespace App\Http\Controllers\Frontend\User;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Models\Office\OfficeStanding;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;


/**
 * Class AccountController.
 */
class AccountController extends Controller
{
	/**
	 * @return Factory|View
	 */
	public function index()
	{
		$officeUser = OfficeStanding::with('user.roles')->where('id', Auth::user()->office)->get();
		$user       = $office = User::where('id', auth()->id())->first();
		$office     = OfficeStanding::where('id', $user->office)->first();
		return view('frontend.user.account', compact('office', 'officeUser'));
	}
}
