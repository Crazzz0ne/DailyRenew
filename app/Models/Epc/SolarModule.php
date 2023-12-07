<?php


namespace App\Models\Epc;


use App\Models\Epc\Traits\SolarModulesRelationship;
use Illuminate\Database\Eloquent\Model;

class SolarModule  extends Model
{
    protected $guarded = [];

    use SolarModulesRelationship;
}
