<?php

namespace App\Models\Auth;


use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Spatie\Permission\Traits\HasRoles;

class BaseUserHasPosition extends Authenticatable implements AuditableInterface
{
	use Auditable,
		HasRoles;

	protected $table = 'user_has_position';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'position_id',
	];


}
