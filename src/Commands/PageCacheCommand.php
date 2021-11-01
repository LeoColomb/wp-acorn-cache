<?php

namespace LeoColomb\WPAcornCache\Commands;

use LeoColomb\WPAcornCache\ObjectCache;
use LeoColomb\WPAcornCache\PageCache;
use WP_CLI;
use WP_CLI_Command;

use function app;

/**
 * WordPress Acorn Page Cache commands.
 *
 * ## EXAMPLES
 *
 *     # Print cache status.
 *     $ wp page-cache status
 */
class PageCacheCommand extends WP_CLI_Command
{
    /**
     * Flush the page cache, clear all data.
     *
     * ## OPTIONS
     *
     * <url>
     * : The key of the value.
     *
     * ## EXAMPLES
     *
     *     wp page-cache purge [url]
     */
    public function purge(array $args): void
    {
        \Roots\bootloader();

        if ([$url] = $args){
            app(PageCache::class)->getStore()->purge($url);
        } else {
            app(PageCache::class)->getStore()->cleanup();
        }

        WP_CLI::success('Purged!');
    }
}
