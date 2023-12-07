<?php

namespace App\Listeners\Backend\Auth\User;

use App\Events\Backend\Auth\User\UserConfirmed;
use App\Events\Backend\Auth\User\UserCreated;
use App\Events\Backend\Auth\User\UserDeactivated;
use App\Events\Backend\Auth\User\UserDeleted;
use App\Events\Backend\Auth\User\UserPasswordChanged;
use App\Events\Backend\Auth\User\UserPermanentlyDeleted;
use App\Events\Backend\Auth\User\UserReactivated;
use App\Events\Backend\Auth\User\UserRestored;
use App\Events\Backend\Auth\User\UserSocialDeleted;
use App\Events\Backend\Auth\User\UserUnconfirmed;
use App\Events\Backend\Auth\User\UserUpdated;
use Illuminate\Events\Dispatcher;
use Log;

/**
 * Class UserEventListener.
 */
class UserEventListener
{
	/**
	 * @param $event
	 */
	public function onCreated($event)
	{
		Log::info('User Created');
	}

	/**
	 * @param $event
	 */
	public function onUpdated($event)
	{
		Log::info('User Updated');
	}

	/**
	 * @param $event
	 */
	public function onDeleted($event)
	{
		Log::info('User Deleted');
	}

	/**
	 * @param $event
	 */
	public function onConfirmed($event)
	{
		Log::info('User Confirmed');
	}

	/**
	 * @param $event
	 */
	public function onUnconfirmed($event)
	{
		Log::info('User Unconfirmed');
	}

	/**
	 * @param $event
	 */
	public function onPasswordChanged($event)
	{
		Log::info('User Password Changed');
	}

	/**
	 * @param $event
	 */
	public function onDeactivated($event)
	{
		Log::info('User Deactivated');
	}

	/**
	 * @param $event
	 */
	public function onReactivated($event)
	{
		Log::info('User Reactivated');
	}

	/**
	 * @param $event
	 */
	public function onSocialDeleted($event)
	{
		Log::info('User Social Deleted');
	}

	/**
	 * @param $event
	 */
	public function onPermanentlyDeleted($event)
	{
		Log::info('User Permanently Deleted');
	}

	/**
	 * @param $event
	 */
	public function onRestored($event)
	{
		Log::info('User Restored');
	}

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param Dispatcher $events
	 */
	public function subscribe($events)
	{
		$events->listen(
			UserCreated::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onCreated'
		);

		$events->listen(
			UserUpdated::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onUpdated'
		);

		$events->listen(
			UserDeleted::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onDeleted'
		);

		$events->listen(
			UserConfirmed::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onConfirmed'
		);

		$events->listen(
			UserUnconfirmed::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onUnconfirmed'
		);

		$events->listen(
			UserPasswordChanged::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onPasswordChanged'
		);

		$events->listen(
			UserDeactivated::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onDeactivated'
		);

		$events->listen(
			UserReactivated::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onReactivated'
		);

		$events->listen(
			UserSocialDeleted::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onSocialDeleted'
		);

		$events->listen(
			UserPermanentlyDeleted::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onPermanentlyDeleted'
		);

		$events->listen(
			UserRestored::class,
			'App\Listeners\Backend\Auth\User\UserEventListener@onRestored'
		);
	}
}
