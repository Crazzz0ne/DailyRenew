<?php


namespace App\Models\Epc;


use App\Models\Epc\Traits\EpcCreditRelationship;
use Illuminate\Database\Eloquent\Model;

class EpcCreditStatus extends Model
{
    use EpcCreditRelationship;


    protected $table='epc_credit_status';



}
