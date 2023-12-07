<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		Horizon::routeSmsNotificationsTo('6199406423');
		Horizon::routeMailNotificationsTo('chris.furman@solcalenergy.com');

		// Horizon::routeSlackNotificationsTo('Slack-webhook-url', '#channel');

		// Horizon::night();
	}

	/**
	 * Register the Horizon gate.
	 *
	 * This gate determines who can access Horizon in non-local environments.
	 *
	 * @return void
	 */
	protected function gate()
	{
		Gate::define('viewHorizon', function ($user) {
			return in_array($user->email, [
				'shane@solarbrightwave.com',
				'chris.furman@solcalenergy.com'
			]);
		});
	}
}
