<?php


namespace App\Models\Office\Market\PowerCompany\Traits\Relationships;


use App\Models\Office\Market\PowerCompany\PowerCompany;

trait ProgramRelationship
{
    public function powerCompany()
    {
        return $this->belongsTo(PowerCompany::class);
    }
}
