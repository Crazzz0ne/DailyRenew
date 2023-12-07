<?php


namespace App\Models\SalesFlow\Customer;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;


class BaseCustomer extends Model  implements AuditableInterface
{

    use Auditable;
	use SoftDeletes;
    protected $primaryKey = 'id';
	protected $table = 'customers';

    protected $guarded = [];
    protected $auditExclude = [

    ];



}
