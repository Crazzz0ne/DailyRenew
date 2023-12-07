<?php

namespace App\Models\VendorLink\Traits\Relationship;

use App\Models\VendorLink\Vendor;

//use App\LinkHasVendor;

/**
 * Class LinkRelationship.
 */
trait LinkLoginRelationship
{


	public function vendor()
	{
		return $this->belongsTo(Vendor::class);
	}
}
