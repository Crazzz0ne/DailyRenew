<?php


namespace App\Models\Epc\Traits;


use App\Models\Epc\Epc;

trait EpcAddersRelationship
{

    public function epc()
    {
        return $this->belongsTo(Epc::class);
    }

}
