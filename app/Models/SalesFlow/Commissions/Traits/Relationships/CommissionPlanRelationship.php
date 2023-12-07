<?php

namespace App\Models\SalesFlow\Commissions\Traits\Relationships;

use App\Models\Office\Office;

trait CommissionPlanRelationship
{
    public function office()
    {
        return $this->hasMany(Office::class);
    }
}
