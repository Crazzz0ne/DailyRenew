<?php


namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;
use App\Models\Commission\Traits\Relationships\CommissionTypesRelationship;

class CommissionTypes extends Model
{
    use CommissionTypesRelationship;

}
