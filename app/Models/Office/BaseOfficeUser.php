<?php

namespace App\Models\Office;

use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseOfficeUser extends Authenticatable implements AuditableInterface
{
	use Auditable;

	protected $fillable = [
		'user_id',
		'office_id'
	];

}
