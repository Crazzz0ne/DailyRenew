<?php

namespace App\Models\Auth;

use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * Class BaseLinkHasOffice.
 */
class BaseLinkHasOffice extends Authenticatable implements AuditableInterface
{
	use Auditable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'link_id',
		'vendor_id',
	];
}
