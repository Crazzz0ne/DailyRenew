<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Line;

trait LineRelationship
{
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function requestingUser()
    {
        return $this->belongsTo(User::class, 'requested_user_id', 'id');
    }

    public function filledUser()
    {
        return $this->belongsTo(User::class, 'filled_user_id', 'id');
    }

    public function customer()
    {
        return $this->hasOneThrough(Customer::class, Lead::class, 'customer_id', 'id',  'id', 'id');
    }

    public function related()
    {
        return $this->hasMany(Line::class, 'lead_id', 'lead_id');
    }


}
