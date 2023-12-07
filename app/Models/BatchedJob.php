<?php

namespace App\Models;

use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchedJob extends Model
{
    use SoftDeletes;
    use Timestamp;

    protected $fillable = [
        'type',
        'data',
        'lead_id'
    ];

//    protected $casts = [
//        'data' => 'array',
//    ];
}
