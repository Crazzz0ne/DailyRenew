<?php

namespace App\Mail\Office;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OfficeStandingsMailable extends Mailable
{
	use Queueable, SerializesModels;

	public $announcement;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($announcement)
	{
		$this->announcement = $announcement;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$announcement            = $this->announcement;
		$announcement['subject'] = 'should not bee here';


		$this->subject('New Standings, See who the King is');


		return $this->view('mail.office.created', compact('announcement'));
	}
}
