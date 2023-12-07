<?php

namespace App\Models\VendorLink;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseLink extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;


	protected $table = 'links';

	protected $fillable = [
		'sort_id',
		'representative',
		'email',
		'office_phone',
		'cell_phone',
	];

	protected $casts = [
		'active' => 'boolean',
	];
}

