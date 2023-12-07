<?php


namespace App\Models\SalesFlow\Appointment\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;

trait AvailabilityRelationship
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
