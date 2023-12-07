<?php


namespace App\Models\SalesFlow\Lead\System;


use App\Models\SalesFlow\Lead\System\Traits\Relationship\ProposedSystemRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class ProposedSystem extends Model implements AuditableInterface
{

    use ProposedSystemRelationship,
        SoftDeletes,
        Auditable;

    protected $table = 'proposed_systems';

    protected $guarded = [

    ];
    protected $auditExclude = [

    ];

    protected $casts = [
        'adders' => 'array',
        'solar_rate' => 'float',
        'system_size' => 'float',
        'monthly_payment' => 'float',
        'contract_amount' => 'float',
        'ppw' => 'decimal:2',
    ];
}
