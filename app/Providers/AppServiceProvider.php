<?php

namespace App\Providers;

use App\Channels\DatabaseChannel;
use App\Models\SalesFlow\Lead\Lead;
use App\Observers\SalesFlow\LeadObserver;
use Barryvdh\Debugbar\Facade;
use Carbon\Carbon;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Mail\Mailer;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register()
	{
		// Sets third party service providers that are only needed on local/testing environments
		if ($this->app->environment() !== 'production') {
			/**
			 * Loader for registering facades.
			 */
			$loader = AliasLoader::getInstance();

			// Load third party local aliases
			$loader->alias('Debugbar', Facade::class);
		}

        $this->app->bind('user.mailer', function ($app, $parameters) {
            $smtp_host = array_get($parameters, 'smtp_host');
            $smtp_port = array_get($parameters, 'smtp_port');
            $smtp_username = array_get($parameters, 'smtp_username');
            $smtp_password = array_get($parameters, 'smtp_password');
            $smtp_encryption = array_get($parameters, 'smtp_encryption');

            $from_email = array_get($parameters, 'from_email');
            $from_name = array_get($parameters, 'from_name');

            $from_email = $parameters['from_email'];
            $from_name = $parameters['from_name'];

            $transport = new \Swift_SmtpTransport($smtp_host, $smtp_port);
            $transport->setUsername($smtp_username);
            $transport->setPassword($smtp_password);
            $transport->setEncryption($smtp_encryption);

            $swift_mailer = new \Swift_Mailer($transport);

            $mailer = new Mailer($app->get('view'), $swift_mailer, $app->get('events'));
            $mailer->alwaysFrom($from_email, $from_name);
            $mailer->alwaysReplyTo($from_email, $from_name);

            return $mailer;
        });


	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot()
	{
		/*
		 * Application locale defaults for various components
		 *
		 * These will be overridden by LocaleMiddleware if the session local is set
		 */

		// setLocale for php. Enables ->formatLocalized() with localized values for dates
		setlocale(LC_TIME, config('app.locale_php'));

		// setLocale to use Carbon source locales. Enables diffForHumans() localized
		Carbon::setLocale(config('app.locale'));

		/*
		 * Set the session variable for whether or not the app is using RTL support
		 * For use in the blade directive in BladeServiceProvider
		 */
		if (!app()->runningInConsole()) {
			if (config('locale.languages')[config('app.locale')][2]) {
				session(['lang-rtl' => true]);
			} else {
				session()->forget('lang-rtl');
			}
		}

		// Force SSL in production
		/*if ($this->app->environment() === 'production') {
			URL::forceScheme('https');
		}*/

		// Set the default string length for Laravel5.4
		// https://laravel-news.com/laravel-5-4-key-too-long-error
		Schema::defaultStringLength(191);

		// Set the default template for Pagination to use the included Bootstrap 4 template
		AbstractPaginator::defaultView('pagination::bootstrap-4');
		AbstractPaginator::defaultSimpleView('pagination::simple-bootstrap-4');

		// Custom Blade Directives

		/*
		 * The block of code inside this directive indicates
		 * the project is currently running in demo mode.
		 */
		Blade::if('demo', function () {
			return config('app.demo');
		});

		/*
		 * The block of code inside this directive indicates
		 * the chosen language requests RTL support.
		 */
		Blade::if('langrtl', function ($session_identifier = 'lang-rtl') {
			return session()->has($session_identifier);
		});

		//Tracking changes to the lead
        Lead::observe(LeadObserver::class);

        //Replace the default notification DB channel with our own, this allows us to do easy batch tracking
        $this->app->instance(\Illuminate\Notifications\Channels\DatabaseChannel::class, new DatabaseChannel);
    }
}
