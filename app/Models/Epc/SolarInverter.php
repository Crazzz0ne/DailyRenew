<?php


namespace App\Models\Epc;


use App\Models\Epc\Traits\SolarInverterRelationship;
use Illuminate\Database\Eloquent\Model;

class SolarInverter extends Model
{
    protected $guarded = [];

    use SolarInverterRelationship;
}
