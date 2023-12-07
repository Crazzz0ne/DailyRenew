<?php


namespace App\Models\SalesFlow\Lead;

use App\Models\SalesFlow\Lead\Traits\Relationship\LeadUtilityRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class LeadUtility extends Model implements AuditableInterface
{
    use LeadUtilityRelationship;
    use SoftDeletes;
    use Auditable;


    protected $guarded = [];

    protected $casts = [

    ];

    protected $auditExclude = [

    ];

}
