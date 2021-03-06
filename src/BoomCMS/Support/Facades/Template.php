<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Template extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.repositories.template';
    }
}
