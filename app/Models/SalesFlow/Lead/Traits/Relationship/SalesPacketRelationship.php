<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\SalesFlow\Lead\Lead;

trait SalesPacketRelationship
{
    public function lead()
    {
        return $this->hasOne(Lead::class);
    }
}
