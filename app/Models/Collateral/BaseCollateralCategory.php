<?php


namespace App\Models\Collateral;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseCollateralCategory extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;

	protected $table = 'collateral_categories';

	protected
		$fillable = [
		'name',
		'description',
		'url'
	];
}
