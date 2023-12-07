<?php

namespace App\Models\Office\Traits\Relationship;

use App\Models\Office\Market\Market;
use  App\Models\Office\Office;
use App\Models\Office\OfficeOptions;
use App\Models\RoundRobin\RoundRobin;
use App\Models\SalesFlow\Commissions\CommissionPlan;


trait OfficeRelationship
{
    public function User()
    {
        return $this->hasMany(\App\Models\Auth\User::class);
    }

    public function Market()
    {
        return $this->belongsTo(Market::class);
    }

    public function options()
    {
        return $this->hasOne(OfficeOptions::class, 'office_id', 'id');
    }

    public function roundRobin()
    {
        return $this->hasMany(RoundRobin::class);
    }

    public function commissionPlan()
    {
        return $this->belongsTo(CommissionPlan::class, 'commission_plan_id', 'id');
    }



}
