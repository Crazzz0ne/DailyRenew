<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\RoofType;

trait LeadRoofRelationship
{
	public function lead()
	{
		return $this->belongsTo(Lead::class);
	}

    public function type()
    {
        return $this->belongsTo(RoofType::class, 'roof_type_id');
    }
}
