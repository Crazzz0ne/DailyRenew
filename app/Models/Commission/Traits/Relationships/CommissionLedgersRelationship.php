<?php


namespace App\Models\Commission\Traits\Relationships;


use App\Models\Auth\User;
use App\Models\Commission\CommissionTypes;
use App\Models\Office\Office;
use App\Models\SalesFlow\Lead\Lead;

trait CommissionLedgersRelationship
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function type()
    {
        return $this->belongsTo(CommissionTypes::class);
    }

}
