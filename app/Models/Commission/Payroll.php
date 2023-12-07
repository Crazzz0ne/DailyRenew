<?php


namespace App\Models\Commission;


use App\Models\Commission\Traits\Relationships\PayrollRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use SoftDeletes;
    use PayrollRelationship;
    protected $guarded = [];

    protected $casts = [
        'commissions' => 'array',
    ];

}
