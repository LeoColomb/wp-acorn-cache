<?php

namespace LeoColomb\WPAcornCache\Facades;

use LeoColomb\WPAcornCache\Caches\ObjectCache as ObjectCacheAccessor;
use Illuminate\Support\Facades\Facade;

class ObjectCache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ObjectCacheAccessor::class;
    }
}
