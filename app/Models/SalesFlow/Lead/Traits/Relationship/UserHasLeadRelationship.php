<?php


namespace App\Models\SalesFlow\Lead\Traits\Relationship;


use App\Models\Auth\User;
use App\Models\SalesFlow\Lead\Lead;
use App\Models\SalesFlow\Position\Position;

trait UserHasLeadRelationship
{
	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function leads()
    {
	    return $this->belongsTo(Lead::class, 'lead_id', 'id');
    }



}
