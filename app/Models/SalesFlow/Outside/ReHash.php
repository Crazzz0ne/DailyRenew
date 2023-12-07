<?php

namespace App\Models\SalesFlow\Outside;

use App\Models\SalesFlow\Lead\Lead;
use Illuminate\Database\Eloquent\Model;

class ReHash extends Model
{
    protected $table = 're_hash_out_side';
    protected $guarded = [];

    public function lead()
    {
        return $this->hasOne(Lead::class);
    }
}
