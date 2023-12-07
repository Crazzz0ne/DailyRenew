<?php


namespace App\Models\Collateral\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\Collateral\CollateralCategory;

class CollateralContentRelationship
{
	public function Categories()
	{
		return $this->belongsTo(CollateralCategory::class, 'category_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'auth_by');
	}
}
