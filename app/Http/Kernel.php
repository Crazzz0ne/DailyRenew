<?php

namespace App\Http;


use App\Http\Middleware\AbortIfNotOfficeOwner;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckForDemoMode;
use App\Http\Middleware\CheckForMaintenanceMode;

use App\Http\Middleware\ConvertStringBooleansToBooleans;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\ManagerEfficiency;
use App\Http\Middleware\PasswordExpires;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\ToBeLoggedOut;
use App\Http\Middleware\TrackUsage;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use Fruitcake\Cors\HandleCors;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;


/**
 * Class Kernel.
 */
class Kernel extends HttpKernel
{
	/**
	 * The application's global HTTP middleware stack.
	 *
	 * These middleware are run during every request to your application.
	 *
	 * @var array
	 */
	protected $middleware = [
        HandleCors::class,
		CheckForMaintenanceMode::class,
		CheckForDemoMode::class,
		ValidatePostSize::class,
		TrimStrings::class,
		ConvertEmptyStringsToNull::class,
		TrustProxies::class,
	];

	/**
	 * The application's route middleware groups.
	 *
	 * @var array
	 */
	protected $middlewareGroups = [
		'web' => [
		    EncryptCookies::class,
			AddQueuedCookiesToResponse::class,
			StartSession::class,
			AuthenticateSession::class, // Must be enabled for 'single login' to work
			ShareErrorsFromSession::class,
			VerifyCsrfToken::class,
			LocaleMiddleware::class,
			SubstituteBindings::class,
			ToBeLoggedOut::class,
//			ManagerEfficiency::class,
		],

		'api' => [
			'throttle:600,1',
			'bindings',
//            TrackUsage::class,
		],

		'admin' => [
			'auth',
			'password_expires',

		],
        'webhook' => [

        ]
	];

	/**
	 * The application's route middleware.
	 *
	 * These middleware may be assigned to groups or used individually.
	 *
	 * @var array
	 */
	protected $routeMiddleware = [
		'auth' => Authenticate::class,
		'auth.basic' => AuthenticateWithBasicAuth::class,
		'bindings' => SubstituteBindings::class,
		'cache.headers' => SetCacheHeaders::class,
		'can' => Authorize::class,
		'guest' => RedirectIfAuthenticated::class,
		'password_expires' => PasswordExpires::class,
		'signed' => ValidateSignature::class,
		'throttle' => ThrottleRequests::class,
		'verified' => EnsureEmailIsVerified::class,
		'role_or_permissions' => RoleOrPermissionMiddleware::class,
		'office_owner' => AbortIfNotOfficeOwner::class,
		'permission' => PermissionMiddleware::class,
		'role' => RoleMiddleware::class,
	];

	/**
	 * The priority-sorted list of middleware.
	 *
	 * This forces non-global middleware to always be in the given order.
	 *
	 * @var array
	 */
	protected $middlewarePriority = [
		StartSession::class,
		ShareErrorsFromSession::class,
		Authenticate::class,
		AuthenticateSession::class,
		SubstituteBindings::class,
		Authorize::class,
	];
}
