<?php

namespace App\Http\Middleware;

use App\Models\Auth\User;
use App\Models\Office\ManagerEfficiency as ManagerEfficiencyModel;
use Carbon\Carbon;
use Closure;
use Request;

class ManagerEfficiency
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		$method = Request::method();
		$user   = User::has('officeHasUser', '=', 1)->pluck('id');

		if ($method != 'POST') {
			if ($request->user() && $request->user()->manager_efficiency && $request->route()->uri != 'dashboard/managerefficiency/create') {
				$now       = Carbon::now();
				$month     = $now->month;
				$year      = $now->year;
				$twentyith = Carbon::createFromDate($year, $month, 20);

				if ($twentyith <= $now) {
					$check = ManagerEfficiencyModel::whereIn('user_id', $user)
						->whereBetween('created_at',
							[$now->startOfMonth()->toDateTimeString(),
								$now->endOfMonth()->toDateTimeString()])
						->count();
					if (!$check) {
						return redirect()->route('dashboard.managerefficiency.create')
							->withFlashSuccess(__('Please fill out this form to restore access'));
					}
				}
			}
		}
		return $next($request);
	}
}
