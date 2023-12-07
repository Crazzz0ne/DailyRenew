<?php


namespace App\Models\SalesFlow\Lead;


use App\Models\SalesFlow\Lead\Traits\Relationship\LoginInfoRelationship;

class LeadLogin extends BaseLeadLogin
{
	use LoginInfoRelationship;

}
