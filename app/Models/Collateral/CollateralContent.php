<?php

namespace App\Models\Collateral;

use App\Models\Collateral\Traits\Relationship\CollateralCategoryRelationship;


class CollateralContent extends BaseCollateralContent
{
	use CollateralCategoryRelationship;
}
