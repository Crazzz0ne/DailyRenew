<?php

namespace App\Models\SalesFlow\MassText;
use Arcanedev\Support\Database\Model;

class MassText extends Model
{
//    public $sent;
//    public $customer_name;
//    public $sender_number;
//    public $customer_number;

    protected $fillable = [
        'customer_number',
        'customer_name',
        'sending_number',
        'sent',
        'sent_date'
    ];

    protected $casts = [
        'sent' => 'boolean',
    ];

}
