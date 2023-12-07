<?php

namespace App\Models\Office\Market\PowerCompany\Traits\Relationships;

use App\Models\Office\Market\Market;
use App\Models\Office\Market\PowerCompany\Program;

trait PowerCompanyRelationship
{
    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class);
    }



}
