<?php

namespace App\Models\VendorLink\Traits\Relationship;


use App\Models\VendorLink\Link;

trait CategoryRelationship
{
	public function links()
	{
		return $this->hasMany(Link::class, 'category_id', 'id');
	}

}
