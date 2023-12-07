<?php

namespace App\Mail\Support;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMailable extends Mailable
{
	use Queueable, SerializesModels;
	public $support;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($support)
	{
		$this->support = $support;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$support = $this->support;

		$this->subject('New Problem');

		return $this->view('mail.support.created', compact('support'));
	}
}
