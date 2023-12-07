<?php


namespace App\Models\SalesFlow\Appointment\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\Office\OfficeUser;
use App\Models\SalesFlow\Appointment\AppointmentStatuses;
use App\Models\SalesFlow\Appointment\AppointmentType;
use App\Models\SalesFlow\Lead\Lead;

trait AppointmentRelationship
{
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function type()
    {
        return $this->belongsTo(AppointmentType::class, 'type_id');
    }

    public function users()
    {
        return $this->hasManyThrough(
            OfficeUser::class,
            User::class,
            'id', // Foreign key on users table...
            'office_id', // Foreign key on posts table...
            'id', // Local key on countries table...
            'id' // Local key on users table...
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function status()
    {
        return $this->belongsTo(AppointmentStatuses::class);
    }
}
