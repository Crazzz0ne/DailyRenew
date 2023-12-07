<?php


namespace App\Models\SalesFlow\Position;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BasePosition extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;


	protected $dates = [
		'created_at',

	];

}
