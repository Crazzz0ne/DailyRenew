<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SlackClassFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'slack'; }
}
