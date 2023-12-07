<?php

namespace App\Models\VendorLink;


use App\Models\VendorLink\Traits\Relationship\LinkLoginRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseLinkLogin extends Model implements AuditableInterface
{
	use Auditable,
		LinkLoginRelationship,
		SoftDeletes;

	protected $table = 'link_logins';

	protected $fillable = [
		'user_name',
		'password',
		'link_id',
	];

    protected $auditExclude = [

    ];
}
