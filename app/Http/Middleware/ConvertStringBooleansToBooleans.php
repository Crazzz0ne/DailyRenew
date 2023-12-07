<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertStringBooleansToBooleans
{
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function (&$value, $key) {
            if ($value === 'true') {
                $value = true;
            } elseif ($value === 'false') {
                $value = false;
            }
        });

        $request->merge($input);

        return $next($request);
    }
}
