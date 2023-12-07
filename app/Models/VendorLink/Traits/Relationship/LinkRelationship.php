<?php

namespace App\Models\VendorLink\Traits\Relationship;

use App\Models\VendorLink\Category;
use App\Models\VendorLink\LinkLogin;
use App\Models\VendorLink\Vendor;

//use App\LinkHasVendor;

/**
 * Class LinkRelationship.
 */
trait LinkRelationship
{
	/**
	 * @return mixed
	 */
	public function vendors()
	{
		return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
	}

	public function categories()
	{
		return $this->belongsTo(Category::class, 'category_id', 'id');
	}

	public function passwords()
	{
		return $this->hasMany(LinkLogin::class);
	}

}
