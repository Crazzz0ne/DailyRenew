<?php


namespace App\Models\Mastermind\Traits\Relationship;


use App\Models\MasterMind\MastermindCategory;


trait  MastermindContentRelationship
{
	public function Categories()
	{
		return $this->belongsTo(MastermindCategory::class, 'category_id', 'id');
	}
}
