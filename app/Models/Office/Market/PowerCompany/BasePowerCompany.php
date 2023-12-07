<?php
namespace App\Models\Office\Market\PowerCompany;

use OwenIt\Auditing\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BasePowerCompany extends Authenticatable implements AuditableInterface
{
    use Auditable,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'market_id',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */


    /**
     * @var array
     */
    protected $dates = [
        'created_at',
    ];


}
