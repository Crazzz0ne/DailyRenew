<?php

namespace App\Models\VendorLink;


class Category extends BaseCategory
{
	public $primaryKey = 'id';

	// Primary Key
	public $timestamps = true;

	// Timestamps
	protected $table = 'categories';


}
