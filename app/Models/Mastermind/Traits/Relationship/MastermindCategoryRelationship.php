<?php

namespace App\Models\Mastermind\Traits\Relationship;


use App\Models\MasterMind\MastermindContent;

trait MastermindCategoryRelationship
{
	public function Content()
	{
		return $this->hasmany(MastermindContent::class, 'category_id', 'id');
	}
}
