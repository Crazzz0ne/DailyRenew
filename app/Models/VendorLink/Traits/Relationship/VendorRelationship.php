<?php

namespace App\Models\VendorLink\Traits\Relationship;


use App\Models\VendorLink\Link;
use App\Models\VendorLink\LinkLogin;

trait VendorRelationship
{
	public function Links()
	{
		return $this->hasMany(Link::class, 'vendor_id', 'id');
	}

	public function passwords()
	{
		return $this->hasMany(LinkLogin::class, 'vendor_id', 'id');
	}


}
