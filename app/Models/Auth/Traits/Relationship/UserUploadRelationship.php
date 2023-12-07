<?php


namespace App\Models\Auth\Traits\Relationship;


use App\Models\Auth\User;

trait UserUploadRelationship
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
