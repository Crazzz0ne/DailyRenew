<?php


namespace App\Models\SalesFlow\Customer\Traits\Scope;


use App\Models\SalesFlow\Customer\Customer;

trait CustomerScope
{
    function scopeWithName($query, $name)
    {
        // Split each Name by Spaces
        $names = explode(" ", $name);

        // Search each Name Field for any specified Name
        return Customer::where(function($query) use ($names) {
            $query->whereIn('first_name', $names);
            $query->orWhere(function($query) use ($names) {
                $query->whereIn('last_name', $names);
            });
        });
    }
}
