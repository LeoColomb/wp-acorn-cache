<?php

/**
 * Advanced Cache API
 *
 * Caching page requests
 */

if (! class_exists('\\LeoColomb\\WPAcornCache\\Caches\\PageCache')) {
    return;
}

use Illuminate\Support\Facades\Request;
use LeoColomb\WPAcornCache\Caches\PageCache;

\Roots\bootloader();
app(PageCache::class)->handle(Request::instance());
