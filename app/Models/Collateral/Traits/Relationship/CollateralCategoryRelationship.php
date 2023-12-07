<?php

namespace App\Models\Collateral\Traits\Relationship;

use App\Models\Collateral\CollateralContent;

trait CollateralCategoryRelationship
{
	public function Content()
	{
		return $this->hasmany(CollateralContent::class, 'category_id', 'id');
	}
}
