<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class RedirectIfAuthenticated.
 */
class RedirectIfAuthenticated
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @param string|null $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		if (auth()->guard($guard)->check()) {
			return redirect()->route(home_route());
		}

		return $next($request);
	}
}
