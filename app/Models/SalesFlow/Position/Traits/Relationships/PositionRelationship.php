<?php


namespace App\Models\SalesFlow\Position\Traits\Relationships;


use App\Models\Auth\User;

trait PositionRelationship
{
	public function users()
	{
		return $this->hasMany(User::class);
	}
}
