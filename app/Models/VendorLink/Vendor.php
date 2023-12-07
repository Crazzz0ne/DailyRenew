<?php

namespace App\Models\VendorLink;


use App\Models\VendorLink\Traits\Relationship\VendorRelationship;


//use Illuminate\Database\Eloquent\Model;

class Vendor extends BaseVendor
{
	public $primaryKey = 'id';
	public $timestamps = true;
	protected $table = 'vendors';

	use VendorRelationship;


//    public function links()
//    {
//        return $this->hasMany(\App\Models\App\Link::class, 'vendor_id', 'id');
//    }


}
