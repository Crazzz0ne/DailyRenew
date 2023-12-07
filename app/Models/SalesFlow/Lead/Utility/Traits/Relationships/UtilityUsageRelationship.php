<?php


namespace App\Models\SalesFlow\Lead\Utility\Traits\Relationships;


use App\Models\SalesFlow\Customer\Customer;
use App\Models\SalesFlow\Lead\LeadUtility;

trait UtilityUsageRelationship
{
    public function utility()
    {
        return $this->belongsTo(LeadUtility::class, 'id', 'utility_id');
    }

}
