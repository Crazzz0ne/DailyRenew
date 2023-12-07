<?php


namespace App\Models\Office;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseManagerEfficiency extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'office_id',
		'user_id',
		'canvassaers_openers_closers_avg',
		'manager_avg',
		'others',
		'truth',
	];


	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'truth' => 'boolean',
	];

	/**
	 * @var array
	 */
	protected $dates = [
		'created_at',

	];


}
