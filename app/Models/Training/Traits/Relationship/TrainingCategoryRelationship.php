<?php

namespace App\Models\Training\Traits\Relationship;

use App\Models\Training\TrainingContent;

trait TrainingCategoryRelationship
{
	public function Content()
	{
		return $this->hasmany(TrainingContent::class, 'category_id', 'id');
	}
}
