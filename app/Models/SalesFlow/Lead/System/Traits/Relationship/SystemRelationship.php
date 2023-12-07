<?php


namespace App\Models\SalesFlow\Lead\System\Traits\Relationship;


use App\Models\Epc\EpcEquipment;
use App\Models\Epc\EpcFinance;
use App\Models\Epc\SolarInverter;
use App\Models\Epc\SolarModule;
use App\Models\SalesFlow\Lead\Lead;

trait SystemRelationship
{
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
    public function finance()
    {
        return$this->hasOne(EpcFinance::class, 'id', 'epc_finance_id');
    }

    public function module()
    {
        return $this->hasOne(SolarModule::class, 'id', 'modules_id');
    }
    public function inverter()
    {
        return $this->hasOne(SolarInverter::class, 'id', 'inverter_id');
    }
}
