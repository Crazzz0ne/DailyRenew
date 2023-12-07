<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;

trait LeadUploadRelationship
{

	public function lead()
	{
		return $this->belongsTo(Lead::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
