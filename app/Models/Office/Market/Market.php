<?php

namespace App\Models\Office\Market;



use App\Models\Office\Market\Traits\Relationships\MarketRelationship;
use Illuminate\Database\Eloquent\Model;

class Market extends BaseMarket
{
    use MarketRelationship;

    protected $table = 'markets';
}
