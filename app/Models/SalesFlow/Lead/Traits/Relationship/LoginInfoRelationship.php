<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;

use App\Models\SalesFlow\Lead\Lead;

trait LoginInfoRelationship
{
	public function lead()
	{
		return $this->belongsTo(Lead::class);
	}
}
