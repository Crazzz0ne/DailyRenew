<?php

namespace App\Providers;

use App\Channels\DatabaseChannel;
use App\Models\SalesFlow\Lead\Lead;
use App\Notifications\Slack\SlackService;
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
class SlackServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register()
	{

	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot()
	{
		$this->app->bind('slack', function() {
            return new SlackService();
        });
    }
}
