<?php

namespace App\Listeners\Frontend\Auth;

use App\Events\Frontend\Auth\UserConfirmed;
use App\Events\Frontend\Auth\UserLoggedIn;
use App\Events\Frontend\Auth\UserLoggedOut;
use App\Events\Frontend\Auth\UserProviderRegistered;
use App\Events\Frontend\Auth\UserRegistered;
use App\Models\Auth\User;
use App\Models\Traits\Uuid;
use App\Notifications\Frontend\Auth\UserWelcomeNotification;
use App\Notifications\NewEmployeeRegisteredNotification;
use App\Notifications\TwilioWelcomeNotification;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Class UserEventListener.
 */
class UserEventListener
{
	/**
	 * @param $event
	 */
	public function onLoggedIn($event)
	{
		$ip_address = request()->getClientIp();

		// Update the logging in users time & IP
		$event->user->fill([
			'last_login_at' => now()->toDateTimeString(),
			'last_login_ip' => $ip_address,
		]);

		$event->user->save();

//		Log::info('User Logged In: ' . $event->user->full_name);
	}

	/**
	 * @param $event
	 */
	public function onLoggedOut($event)
	{
//		Log::info('User Logged Out: ' . $event->user->full_name);
	}

	/**
	 * @param $event
	 */
	public function onRegistered($event)
	{
        Log::info("New User Created! ", [$event->user]);

        //Notify Managers & Executives
        $handlers =
            User::where('office_id', $event->user->office_id)
                ->role('manager')
                ->orRole('executive')
                ->get();

        //Notify New User
        Notification::send($event->user, new TwilioWelcomeNotification($event));
        Notification::send($event->user, new UserWelcomeNotification($event));
        //Notify Others of new user.
        Notification::send($handlers, new NewEmployeeRegisteredNotification($event, $event->user));
	}

	/**
	 * @param $event
	 */
	public function onProviderRegistered($event)
	{
		Log::info('User Provider Registered: ' . $event->user->full_name);
	}

	/**
	 * @param $event
	 */
	public function onConfirmed($event)
	{
		Log::info('User Confirmed: ' . $event->user->full_name);
	}

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param Dispatcher $events
	 */
	public function subscribe($events)
	{
		$events->listen(
			UserLoggedIn::class,
			'App\Listeners\Frontend\Auth\UserEventListener@onLoggedIn'
		);

		$events->listen(
			UserLoggedOut::class,
			'App\Listeners\Frontend\Auth\UserEventListener@onLoggedOut'
		);

		$events->listen(
			UserRegistered::class,
			'App\Listeners\Frontend\Auth\UserEventListener@onRegistered'
		);

		$events->listen(
			UserProviderRegistered::class,
			'App\Listeners\Frontend\Auth\UserEventListener@onProviderRegistered'
		);

		$events->listen(
			UserConfirmed::class,
			'App\Listeners\Frontend\Auth\UserEventListener@onConfirmed'
		);
	}
}
