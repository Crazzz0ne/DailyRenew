<?php


namespace App\Models\Training;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseTrainingCategory extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes;

	protected $table = 'training_categories';

	protected $fillable = [
		'name',
		'description',
	];


}
