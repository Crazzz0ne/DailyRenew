<?php

namespace App\Models\SalesFlow\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerMessages extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $table = 'customer_messages';

    protected $guarded = [];

    public function leads()
    {
        return $this->hasOne(Customer::class);
    }

    public function images()
    {
        return $this->hasMany(CustomerMessagesImages::class);
    }

}
