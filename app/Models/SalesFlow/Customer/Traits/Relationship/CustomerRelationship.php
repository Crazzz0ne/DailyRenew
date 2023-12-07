<?php


namespace App\Models\SalesFlow\Customer\Traits\Relationship;


use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\LeadRoof;

trait CustomerRelationship
{
	public function leads()
	{
		return $this->hasMany(Lead::class);
	}
}
