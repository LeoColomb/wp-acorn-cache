<?php

/**
 * Advanced Cache API
 */

if (! class_exists('\\LeoColomb\\WPAcornCache\\PageCache')) {
    return;
}

use LeoColomb\WPAcornCache\PageCache;

use function Roots\app;

app(PageCache::class)->handle();
