<?php


namespace App\Models\SalesFlow\Lead;


use Illuminate\Database\Eloquent\Model;

class Disposition extends Model
{

    protected $fillable = [
        'lead_id',
        'stage',
        'reason',
        'user_id'
    ];

}
