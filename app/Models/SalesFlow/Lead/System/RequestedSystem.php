<?php

namespace App\Models\SalesFlow\Lead\System;

use App\Models\SalesFlow\Lead\System\Traits\Relationship\ProposalRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class RequestedSystem extends Model implements AuditableInterface
{
    use ProposalRelationship,
        SoftDeletes,
        Auditable;

    protected $table = 'requested_systems';

    protected $guarded = [];

    protected $casts = [
        'adders' => 'array',
        'approved'=> 'boolean',
        'solar_rate' => 'float',
        'system_size' => 'float',
        'monthly_payment' => 'float'
    ];

}
