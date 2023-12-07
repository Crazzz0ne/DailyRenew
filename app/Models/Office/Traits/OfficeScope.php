<?php


namespace App\Models\Office\Traits;


trait OfficeScope
{


    public function scopeHasRoundRobinType($query, $type)
    {
        return $query->whereHas('roundRobin', function ($query) use ($type) {

            return $query->where('type', $type);
        });
    }

}
