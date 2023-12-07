<?php


namespace App\Models\SalesFlow\Lead;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class BaseLead extends Model implements AuditableInterface
{
    use SoftDeletes;
    use Auditable;
    protected $guarded = [

    ];

    protected $casts = [
        'jeopardy' => 'boolean',
        'stale' => 'date'
    ];
    protected $primaryKey = 'id';
    protected $auditExclude = [

    ];


}
