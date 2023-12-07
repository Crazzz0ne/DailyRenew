<?php


namespace App\Models\Office\Traits\Relationship;


use App\Models\Office\Office;
use App\Models\Office\OfficeStanding;

trait OfficeStandingDataRelationship
{
	public function standingData()
	{
		return $this->belongsTo(OfficeStanding::class);
	}

	public function office()
	{
		return $this->belongsTo(Office::class);
	}

}
