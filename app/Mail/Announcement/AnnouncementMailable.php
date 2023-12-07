<?php

namespace App\Mail\Announcement;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMailable extends Mailable
{
	use Queueable, SerializesModels;

	public $subject;
	public $id;

    /**
     * Create a new message instance.
     *
     * @param $subject
     * @param $id
     */
	public function __construct($subject, $id)
	{
		$this->id = $id;
		$this->subject = $subject;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		$subject = $this->subject;
		$id = $this->id;

		return $this->view('mail.announcement.created', compact('subject', 'id'));
	}
}
