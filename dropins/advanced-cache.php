<?php

/**
 * Advanced Cache API
 */

if (! class_exists('\\LeoColomb\\WPAcornCache\\PageCache')) {
    return;
}

use LeoColomb\WPAcornCache\PageCache;

\Roots\bootloader();
app(PageCache::class)->handleRequest();
