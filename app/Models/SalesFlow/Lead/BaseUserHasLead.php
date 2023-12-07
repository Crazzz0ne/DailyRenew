<?php

namespace App\Models\SalesFlow\Lead;

use App\Models\SalesFlow\Lead\Traits\Relationship\UserHasLeadRelationship;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class BaseUserHasLead extends Model implements AuditableInterface
{

    use Auditable;
    use UserHasLeadRelationship;
    use    SoftDeletes;

    protected $table = 'user_has_leads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'position_id',
        'lead_id',
    ];

    protected $auditExclude = [

    ];
}
