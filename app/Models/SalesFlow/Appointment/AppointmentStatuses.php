<?php

namespace App\Models\SalesFlow\Appointment;

use Illuminate\Database\Eloquent\Model;

class AppointmentStatuses extends Model
{
    protected $table = 'appointment_statuses';
    protected $fillable = [
        'name',
        'deleted_at',
    ];

    public function appointments()
    {
        return $this->belongsTo(Appointment::class, 'status_id', 'id');
    }
}


