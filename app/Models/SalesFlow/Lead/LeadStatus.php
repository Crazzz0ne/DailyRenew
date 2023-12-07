<?php


namespace App\Models\SalesFlow\Lead;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatus extends Model
{
    use SoftDeletes;


    protected $table = 'lead_status';

    protected $guarded = [

    ];



}
