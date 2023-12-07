<?php


namespace App\Models\Office\Traits\Relationship;


use App\Models\Office\Office;

trait OfficeOptionsRelationship
{
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
