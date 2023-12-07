<?php

namespace App\Models\Mastermind;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Spatie\Tags\HasTags;

class BaseMastermindContent extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes,
		HasTags;

	protected $table = 'mastermind_contents';

	protected $fillable = [
		'name',
		'description',
		'category_id',
		'type',
		'url',
		'approved',

	];

	protected $casts = [
		'approved' => 'boolean',
	];
}
