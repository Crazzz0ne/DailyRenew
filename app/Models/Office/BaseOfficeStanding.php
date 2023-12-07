<?php


namespace App\Models\Office;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseOfficeStanding extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;


	protected $table = 'office_standings';

	protected $fillable = [
		'name',
		'sdate',
		'approved',
		'user_id',
	];

	protected $casts = [
		'approved' => 'boolean'
	];
}
