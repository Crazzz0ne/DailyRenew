<?php


namespace App\Models\Office;


use App\Models\Office\Traits\Relationship\OfficeCommissionsRelationship;
use Illuminate\Database\Eloquent\Model;

class OfficeCommissions extends Model
{
    use OfficeCommissionsRelationship;

}
