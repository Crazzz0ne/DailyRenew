<?php

namespace App\Http\Controllers\Backend\Mail;

use App\Announcement;
use App\Http\Controllers\Controller;
use Mail;

class MailController extends Controller
{
	public function test()
	{
		if (auth()->user()->isSuper()) {
			return 'true';
		} else {
			return 'false';
		}
	}


	public function sendEmail()
	{
		$data['title'] = "This is Test Mail Tuts Make";

		Mail::send('mail.announcement.mail', $data, function ($message) {

			$message->to('tutsmake@gmail.com', 'Receiver Name')
				->subject('Tuts Make Mail');
		});

		if (Mail::failures()) {
			return response()->Fail('Sorry! Please try again latter');
		} else {
			return response()->Sucess('Great! Successfully send in your mail');
		}
	}
}
