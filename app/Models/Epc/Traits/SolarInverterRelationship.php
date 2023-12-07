<?php


namespace App\Models\Epc\Traits;


use App\Models\Epc\Epc;

trait SolarInverterRelationship
{
    public function epc(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Epc::class);
    }
}
