<?php


namespace App\Models\SalesFlow\Appointment\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\SalesFlow\Appointment\AppointmentType;

trait OpportunityRelationship
{
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function type()
	{
		return $this->belongsTo(AppointmentType::class);
	}
}
