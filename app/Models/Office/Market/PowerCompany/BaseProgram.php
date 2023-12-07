<?php


namespace App\Models\Office\Market\PowerCompany;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseProgram extends Authenticatable implements AuditableInterface
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
        'power_company_id',
        'type'
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
