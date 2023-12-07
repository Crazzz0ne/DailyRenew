<?php


namespace App\Models\Office;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseOfficeStandingData extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;


	protected $table = 'office_standings_data';

	protected $fillable = [
		'name',
		'data',
		'user_id',
		'office_standing_id',
		'office_id'
	];
}
