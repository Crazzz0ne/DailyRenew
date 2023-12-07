<?php

namespace App\Models\Office;

use App\Models\Office\Traits\Relationship\ManagerEfficiencyRelationship;

class ManagerEfficiency extends BaseManagerEfficiency
{
	use ManagerEfficiencyRelationship;


	protected $table = 'manager_efficiencies';
}
