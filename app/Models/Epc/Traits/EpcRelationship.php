<?php


namespace App\Models\Epc\Traits;


use App\Models\Epc\EpcAdders;
use App\Models\Epc\EpcCreditStatus;
use App\Models\Epc\EpcEquipment;

use App\Models\Epc\EpcFinance;
use App\Models\Epc\SolarInverter;
use App\Models\Epc\SolarModule;
use App\Models\Office\Market\PowerCompany\PowerCompany;
use App\Models\SalesFlow\Lead\Lead;

trait EpcRelationship
{
    public function solarModules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SolarModule::class)->where('active', true);
    }

    public function solarInverters(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SolarInverter::class)->where('active', true);
    }

    public function equipment(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EpcEquipment::class)->where('active', true);
    }

    public function adders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EpcAdders::class);
    }

    public function leads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function creditStatus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EpcCreditStatus::class);
    }

    public function powerCompany(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PowerCompany::class)->orderBy('name');
    }

    public function finance(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EpcFinance::class)->orderBy('name');
    }
}
