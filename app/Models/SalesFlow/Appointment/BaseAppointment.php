<?php

namespace App\Models\SalesFlow\Appointment;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class BaseAppointment extends Model implements AuditableInterface
{
    use Auditable;
	use SoftDeletes;



	protected $table = 'appointments';

	protected $guarded = [
		'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

	protected $dates = [
	    'start_time',
        'finish_time',
        'completed_at'
    ];
    protected $casts = [
      'remote' => 'boolean'
    ];

    protected $auditExclude = [

    ];

}
