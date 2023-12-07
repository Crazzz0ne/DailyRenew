<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Contact\SendContactRequest;
use App\Mail\Frontend\Contact\SendContact;
use App\Models\SalesFlow\Appointment\Appointment;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Class ContactController.
 */
class ContactController extends Controller
{
	/**
	 * @return View
	 */
	public function index()
	{

        return Appointment::where('user_id', '=', null)
            ->count();
		return view('frontend.contact');
	}

	/**
	 * @param SendContactRequest $request
	 *
	 * @return mixed
	 */
	public function send(SendContactRequest $request)
	{
		Mail::send(new SendContact($request));

		return redirect()->back()->withFlashSuccess(__('alerts.frontend.contact.sent'));
	}
}
