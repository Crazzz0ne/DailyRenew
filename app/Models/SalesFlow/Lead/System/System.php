<?php


namespace App\Models\SalesFlow\Lead\System;



use App\Models\SalesFlow\Lead\System\Traits\Relationship\SystemRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class System extends Model
{
    protected $table = 'lead_systems';

    use SystemRelationship;
    use SoftDeletes;

    protected $guarded = [

    ];

    protected $casts = [
        'adders' => 'array',
        'solar_rate' => 'float',
        'system_size' => 'decimal:2',
        'monthly_payment' => 'float'
    ];

}
