<?php


namespace App\Models\SalesFlow\Lead;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Exceptions\AuditableTransitionException;
use OwenIt\Auditing\Exceptions\AuditingException;

class BaseLeadLogin extends Model implements AuditableInterface
{

    use Auditable;

    protected $table = 'lead_logins';
    protected $fillable = [
        'user_name',
        'password',
        'lead_id'
    ];

    protected $auditExclude = [

    ];


}
