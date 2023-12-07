<?php


namespace App\Models\Commission;


use App\Models\Commission\Traits\Relationships\CommissionLedgersRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class CommissionLedgers extends Model implements AuditableInterface
{
    use Auditable;
    use SoftDeletes;
    use CommissionLedgersRelationship;

    protected $auditExclude = [];
    public $guarded = [];
}
