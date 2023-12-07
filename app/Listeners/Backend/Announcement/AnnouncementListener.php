<?php

namespace App\Listeners\Backend\Announcement;

use App\Events\Backend\Announcement\AnnouncementCreated;
use App\Events\Backend\Announcement\AnnouncementDeleted;
use App\Events\Backend\Announcement\AnnouncementUpdated;
use App\Mail\Announcement\AnnouncementMailable;
use App\Models\LogThings;
use Illuminate\Events\Dispatcher;
use Log;
use Mail;

class AnnouncementListener
{
	use LogThings;

	/**
	 * @param $event
	 */
	public function onCreated($event)
	{
		Log::info('Announcement Created');

		Mail::send(new AnnouncementMailable($event->announcement));

		if (Mail::failures()) {
			Log::error('Mail failed to send');
		} else {
			$this->success('');
		}
	}

	/**
	 * @param $event
	 */
	public function onUpdated($event)
	{
		Log::info('Announcement Updated');
	}

	/**
	 * @param $event
	 */
	public function onDeleted($event)
	{
		Log::info('Announcement Deleted');
	}

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param Dispatcher $events
	 */
	public function subscribe($events)
	{
		$events->listen(
			AnnouncementCreated::class,
			'App\Listeners\Backend\Announcement\AnnouncementListener@onCreate'
		);

		$events->listen(
			AnnouncementUpdated::class,
			'App\Listeners\Backend\Announcement\AnnouncementListener@onCreate'
		);

		$events->listen(
			AnnouncementDeleted::class,
			'App\Listeners\Backend\Announcement\AnnouncementListener@onCreate'
		);
	}

}
