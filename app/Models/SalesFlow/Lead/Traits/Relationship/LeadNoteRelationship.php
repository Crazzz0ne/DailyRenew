<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;

trait LeadNoteRelationship
{
	public function lead()
	{
		return $this->belongsTo(Lead::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}


    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}
