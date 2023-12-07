<?php

namespace App\Models\Office\Market\Traits\Relationships;

use App\Models\Office\Market\PowerCompany\PowerCompany;
use App\Models\Office\Market\RegionAPI;
use App\Models\Office\Office;

trait MarketRelationship
{
    public function powerCompany()
{
        return $this->hasOne(PowerCompany::class);
    }

    public function office()
    {
        return $this->hasMany(Office::class);
    }

    public function rules()
    {
//        return $this->hasMany(MarketRules::class);
    }

    public function apiKey()
    {
        return $this->hasOne(RegionAPI::class, 'region_id', 'id');
    }

}
