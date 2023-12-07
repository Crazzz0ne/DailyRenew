<?php

namespace App\Models\SalesFlow\Commissions;

use App\Models\SalesFlow\Commissions\Traits\Relationships\CommissionPlanRelationship;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class CommissionPlan extends Model implements AuditableInterface
{
    use Auditable, CommissionPlanRelationship;
    protected $auditExclude = [

    ];
}
