<?php


namespace App\Models\Training\Traits\Relationship;


use App\Models\Training\TrainingCategory;

trait  TrainingContentRelationship
{
	public function Categories()
	{
		return $this->belongsTo(TrainingCategory::class, 'category_id', 'id');
	}
}
