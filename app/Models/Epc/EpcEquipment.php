<?php


namespace App\Models\Epc;


use App\Models\Epc\Traits\EpcEquipmentRelationship;
use Illuminate\Database\Eloquent\Model;

class EpcEquipment extends Model
{
    protected $table = 'epc_equipment';
    protected $guarded = [];

    use EpcEquipmentRelationship;

}
