<?php

namespace App\Models\Training;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Spatie\Tags\HasTags;

class BaseTrainingContent extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes,
		HasTags;

	protected $table = 'training_contents';

	protected $fillable = [
		'name',
		'description',
		'category_id',
		'type',
		'url',

	];
}
