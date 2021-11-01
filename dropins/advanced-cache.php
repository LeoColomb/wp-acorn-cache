<?php

/**
 * Advanced Cache API
 *
 * Caching page requests
 */

if (! class_exists('\\LeoColomb\\WPAcornCache\\PageCache')) {
    return;
}

use Illuminate\Support\Facades\Request;
use LeoColomb\WPAcornCache\PageCache;

\Roots\bootloader();
app(PageCache::class)->handle(Request::instance());
