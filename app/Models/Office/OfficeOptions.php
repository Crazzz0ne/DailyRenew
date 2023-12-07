<?php


namespace App\Models\Office;


use App\Models\Office\Traits\Relationship\OfficeOptionsRelationship;
use Illuminate\Database\Eloquent\Model;

class OfficeOptions extends Model
{
    use OfficeOptionsRelationship;
    protected $guarded =[];

    protected $casts = [
        'roles' => 'array',
        'permissions' => 'array',
        'service_cities' => 'array',
        'require_integrations' => 'boolean'
    ];
}
