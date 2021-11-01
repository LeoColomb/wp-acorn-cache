<?php

namespace LeoColomb\WPAcornCache\Facades;

use LeoColomb\WPAcornCache\ObjectCache as ObjectCacheAccessor;
use Roots\Acorn\Facade as Facade;

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
