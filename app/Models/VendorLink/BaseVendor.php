<?php

namespace App\Models\VendorLink;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class BaseVendor extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;

	protected $table = 'vendors';

	protected $fillable = [
		'is_active',
		'company_name',
		'picture',
	];

	protected $casts = [
		'is_active' => 'boolean'
	];
}
