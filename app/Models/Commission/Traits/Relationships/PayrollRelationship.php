<?php


namespace App\Models\Commission\Traits\Relationships;


use App\Models\Auth\User;

trait PayrollRelationship
{
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
