<?php


namespace App\Models\Office\Market;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseMarketRules extends Authenticatable implements AuditableInterface
{
    use Auditable,
        SoftDeletes;

    protected $table = 'market_rules';

    protected $fillable = [
        'name',
    ];

}
