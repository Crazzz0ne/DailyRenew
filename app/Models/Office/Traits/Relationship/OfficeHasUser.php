<?php

namespace App\Models\Office\Traits\Relationship;

use App\Models\Office;

trait OfficeHasUser
{
	public function office()
	{
		return $this->belongsTo(Office\Office::class, 'office_id', 'user_id');
	}

}
