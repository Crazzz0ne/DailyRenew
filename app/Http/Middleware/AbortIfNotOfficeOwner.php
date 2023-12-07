<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbortIfNotOfficeOwner
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @param string $office
	 * @return mixed
	 */
	public function handle($request, Closure $next, $office)
	{
		if (Auth::user()->hasAnyPermission('administrate all offices') || Auth::user()->hasAnyRole('administrate|executive')) {
			return $next($request);
		} else {

            $selectedOffice  = $request->route()->originalParameters();
            $selectedOffice  = $selectedOffice['office'];
		    if (Auth::user()->office_id !== $office->id){

//                abort(403, 'Unauthorized action.');
            }
            return $next($request);
		}

	}
}
