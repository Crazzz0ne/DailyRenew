<?php


namespace App\Models\Mastermind;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseMastermindCategory extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;

	protected $table = 'mastermind_categories';

	protected $fillable = [
		'name',
		'description',
	];


}
