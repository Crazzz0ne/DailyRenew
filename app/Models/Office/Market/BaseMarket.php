<?php
namespace App\Models\Office\Market;

use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseMarket extends Authenticatable implements AuditableInterface
{
    use Auditable,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

//    protected static function boot()
//    {
//        parent::boot();
////
//        static::addGlobalScope('active', function (Builder $builder) {
//            $builder->where('active', true);
//        });
//    }


}
