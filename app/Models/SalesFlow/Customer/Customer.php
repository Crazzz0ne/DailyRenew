<?php

namespace App\Models\SalesFlow\Customer;

use App\Models\SalesFlow\Customer\Traits\Attribute\CustomerAttribute;
use App\Models\SalesFlow\Customer\Traits\Relationship\CustomerRelationship;
use App\Models\SalesFlow\Customer\Traits\Scope\CustomerScope;


class Customer extends BaseCustomer
{
	use CustomerRelationship,
        CustomerAttribute,
        CustomerScope;

    protected $appends = [
        'full_name'
    ];
    

}
