<?php

namespace LeoColomb\WPAcornCache\Facades;

use LeoColomb\WPAcornCache\AcornCache as AcornCacheAccessor;
use \Roots\Acorn\Facade as Facade;

class AcornCache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return AcornCacheAccessor::class;
    }
}
