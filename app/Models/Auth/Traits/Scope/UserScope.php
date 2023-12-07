<?php

namespace App\Models\Auth\Traits\Scope;

use Carbon\Carbon;

/**
 * Class LeadScope.
 */
trait UserScope
{
	/**
	 * @param $query
	 * @param bool $confirmed
	 *
	 * @return mixed
	 */
	public function scopeConfirmed($query, $confirmed = true)
	{
		return $query->where('confirmed', $confirmed);
	}


	/**
	 * @param $query
	 * @param array $roles
	 *
	 * @return mixed
	 */
	public function scopeHasOffice($query, $user)
	{
		return $query->whereHas('offices', function ($query) use ($user) {
			return $query->whereIn('user_id', $user);
		});
	}

	/**
	 * @param $query
	 * @param bool $status
	 *
	 * @return mixed
	 */
	public function scopeActive($query, $status = true)
	{
		return $query->where('active', $status);
	}

    public function scopeCurrentAppointments($query)
    {
        return $query->whereHas('appointments', function ($query){
//            dd(Carbon::now()->timezone('America/los_angeles')->toDateTimeString());
            $query->where('start_time', '<', Carbon::now()->timezone('America/los_angeles')->toDateTimeString());
            $query->where('type_id', 6);
        });
    }
}
