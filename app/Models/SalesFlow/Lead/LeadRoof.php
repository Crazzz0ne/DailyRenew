<?php

namespace App\Models\SalesFlow\Lead;

use App\Models\SalesFlow\Lead\Traits\Relationship\LeadRoofRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadRoof extends Model
{
    use LeadRoofRelationship, SoftDeletes;

    protected $table = 'lead_roof';
    protected $guarded = [];

}
