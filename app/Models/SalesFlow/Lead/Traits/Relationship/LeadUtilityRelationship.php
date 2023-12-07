<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\Epc\PowerCompany\PowerCompany;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Lead\Utility\UtilityUsage;

trait LeadUtilityRelationship
{

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function powerCompany()
    {
        return $this->hasOne(PowerCompany::class, 'id', 'power_company_id');
    }
    public function usage()
    {
        return $this->hasOne(UtilityUsage::class, 'id', 'utility_id');
    }
}
