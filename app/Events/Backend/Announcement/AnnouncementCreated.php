<?php

namespace App\Events\Backend\Announcement;

use Illuminate\Queue\SerializesModels;


class AnnouncementCreated
{
	use SerializesModels;

	public $announcement;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($announcement)
	{
		$this->announcement;
	}

}
