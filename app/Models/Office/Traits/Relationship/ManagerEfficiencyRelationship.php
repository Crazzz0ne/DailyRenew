<?php


namespace App\Models\Office\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\Office\Office;

trait ManagerEfficiencyRelationship
{

	public function office()
	{
		return $this->belongsTo(Office::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
