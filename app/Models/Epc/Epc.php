<?php

namespace App\Models\Epc;

use App\Models\Epc\Traits\EpcRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Epc extends Model
{
    use EpcRelationship;


    protected $guarded = [];
}
