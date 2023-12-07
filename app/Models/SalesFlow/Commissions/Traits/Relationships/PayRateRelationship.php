<?php

namespace App\Models\SalesFlow\Commissions\Traits\Relationships;

use App\Models\Auth\User;

trait PayRateRelationship
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
