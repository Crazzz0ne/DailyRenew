<?php

namespace App\Models\Collateral;

use App\Models\Collateral\Traits\Relationship\CollateralCategoryRelationship;


class CollateralCategory extends BaseCollateralCategory
{
	use CollateralCategoryRelationship;
}
