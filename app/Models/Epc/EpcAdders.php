<?php


namespace App\Models\Epc;


use App\Models\Epc\Traits\EpcAddersRelationship;
use Illuminate\Database\Eloquent\Model;

class EpcAdders extends Model
{
    use EpcAddersRelationship;

    protected $guarded = [];

}
