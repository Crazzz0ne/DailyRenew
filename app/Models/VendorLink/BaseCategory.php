<?php

namespace App\Models\VendorLink;

use App\Models\VendorLink\Traits\Relationship\CategoryRelationship;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseCategory extends Authenticatable implements AuditableInterface
{
	use Auditable,
		CategoryRelationship,
		SoftDeletes;

	protected $table = 'categories';

	protected $fillable = [
		'sort_order',
		'name',
		'description',
		'active'
	];
}
