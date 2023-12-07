<?php

namespace App\Listeners\Backend\Auth\Role;

use App\Events\Backend\Auth\Role\RoleCreated;
use App\Events\Backend\Auth\Role\RoleDeleted;
use App\Events\Backend\Auth\Role\RoleUpdated;
use Illuminate\Events\Dispatcher;
use Log;

/**
 * Class RoleEventListener.
 */
class RoleEventListener
{
	/**
	 * @param $event
	 */
	public function onCreated($event)
	{
		Log::info('Role Created');
	}

	/**
	 * @param $event
	 */
	public function onUpdated($event)
	{
		Log::info('Role Updated');
	}

	/**
	 * @param $event
	 */
	public function onDeleted($event)
	{
		Log::info('Role Deleted');
	}

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param Dispatcher $events
	 */
	public function subscribe($events)
	{
		$events->listen(
			RoleCreated::class,
			'App\Listeners\Backend\Auth\Role\RoleEventListener@onCreated'
		);

		$events->listen(
			RoleUpdated::class,
			'App\Listeners\Backend\Auth\Role\RoleEventListener@onUpdated'
		);

		$events->listen(
			RoleDeleted::class,
			'App\Listeners\Backend\Auth\Role\RoleEventListener@onDeleted'
		);
	}
}
