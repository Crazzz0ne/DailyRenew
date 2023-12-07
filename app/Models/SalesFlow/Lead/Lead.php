<?php

namespace App\Models\SalesFlow\Lead;


use App\Models\SalesFlow\Lead\Traits\Method\LeadMethod;
use App\Models\SalesFlow\Lead\Traits\Relationship\LeadRelationship;
use App\Models\SalesFlow\Lead\Traits\Scope\LeadScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends BaseLead
{
	use LeadRelationship;
	use LeadScope;
	use LeadMethod;
}
