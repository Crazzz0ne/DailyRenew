<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Response;

/**
 * Class ForgotPasswordController.
 */
class ForgotPasswordController extends Controller
{
	use SendsPasswordResetEmails;

	/**
	 * Display the form to request a password reset link.
	 *
	 * @return Response
	 */
	public function showLinkRequestForm()
	{
		return view('frontend.auth.passwords.email');
	}
}
