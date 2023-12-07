<?php


namespace App\Models\Epc;


use App\Models\Epc\Traits\FinanceRelationship;
use Illuminate\Database\Eloquent\Model;

class EpcFinance  extends Model
{
    protected $table = 'epc_finances';
    protected $guarded = [];

    use FinanceRelationship;

}
