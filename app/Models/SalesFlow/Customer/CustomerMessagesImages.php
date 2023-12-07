<?php

namespace App\Models\SalesFlow\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerMessagesImages extends Model
{
    protected $table = 'customer_messages_images';
    protected $fillable = [
        'customer_message_id',
        'path'
    ];

    public function customerMessage()
    {
        return $this->belongsTo(CustomerMessages::class);
    }

}
