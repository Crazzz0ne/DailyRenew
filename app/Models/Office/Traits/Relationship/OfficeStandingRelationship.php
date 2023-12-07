<?php


namespace App\Models\Office\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\Office\OfficeStandingData;


trait OfficeStandingRelationship
{
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

	public function data()
	{
		return $this->hasMany(OfficeStandingData::class);
	}
}
