<?php

namespace App\Models\Office;

use App\Models\Office\Market\Market;
use App\Models\Office\Traits\OfficeScope;
use App\Models\Office\Traits\Relationship\OfficeRelationship;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Office extends Model
{
	use OfficeRelationship;
	use OfficeScope;
	use HasTags;


    protected $table = 'offices';

    protected $guarded = [];

    protected $casts = [
        'roles' => 'array',
        'permissions' => 'array',
        'active' => 'boolean',
        'require_integrations' => 'boolean',
        'view_ppw' => 'boolean',
        'call_center' => 'boolean',
    ];

    public function market()
    {
        return $this->belongsto(Market::class);
    }

//    protected static function boot()
//    {
//        parent::boot();
////
//        static::addGlobalScope('active', function (Builder $builder) {
//            $builder->where('active', true);
//        });
//    }


}
