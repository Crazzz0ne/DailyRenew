<?php

namespace App\Models\Office;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use Spatie\Tags\HasTags;

class BaseOffice extends Authenticatable implements AuditableInterface
{
	use Auditable,
		SoftDeletes,
        HasTags;


	protected $table = 'offices';

	protected $guarded = [

	];

	protected $casts = [
		'active' => 'boolean',
	];


}
