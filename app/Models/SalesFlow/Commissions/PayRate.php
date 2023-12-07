<?php

namespace App\Models\SalesFlow\Commissions;

use App\Models\SalesFlow\Commissions\Traits\Relationships\PayRateRelationship;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class PayRate extends Model implements AuditableInterface
{
    use Auditable, PayRateRelationship;
    protected $auditExclude = [];


}
