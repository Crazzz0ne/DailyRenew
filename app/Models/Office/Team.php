<?php

namespace App\Models\Office;

use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

class Team  extends Model
{
    protected $guarded =[];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
