<?php

namespace App\Providers;

use App\Http\Composers\Backend\HeaderComposer;
use App\Http\Composers\Backend\SidebarComposer;
use App\Http\Composers\Frontend\NavbarComposer;
use App\Http\Composers\GlobalComposer;
use App\Models\Training\TrainingCategory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ComposerServiceProvider.
 */
class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot()
    {
        // Global
        View::composer(
        // This class binds the $logged_in_user variable to every view
            '*',
            GlobalComposer::class
        );

        // Frontend

        // Backend
        View::composer(
        // This binds items like number of users pending approval when account approval is set to true
            'backend.includes.sidebar',
            SidebarComposer::class
        );

        //Shared
        View::composer(['frontend.training.index', 'backend.training.index'], function ($view) {
            $view->with('categories', TrainingCategory::all());
        });

        View::composer('frontend.includes.nav',
            NavbarComposer::class
        );
        View::composer(
            'backend.includes.header',
            HeaderComposer::class
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }
}
