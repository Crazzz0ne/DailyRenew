<?php


namespace App\Models\RoundRobin;


use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class RoundRobin extends Model implements AuditableInterface
{
    use Auditable;
    protected $guarded = [];
    protected $casts = [
        'list' => 'array',
    ];
    protected $auditExclude = [

    ];

}
