<?php

namespace LeoColomb\WPAcornCache\Commands;

use LeoColomb\WPAcornCache\ObjectCache;
use WP_CLI;
use WP_CLI_Command;

use function app;

/**
 * WordPress Acorn Object Cache commands.
 *
 * ## EXAMPLES
 *
 *     # Print cache status.
 *     $ wp object-cache status
 */
class ObjectCacheCommand extends WP_CLI_Command
{
    /**
     * Show the object cache status.
     *
     * ## EXAMPLES
     *
     *     wp object-cache status
     */
    public function status()
    {
        if (defined('WP_REDIS_DISABLED') && \WP_REDIS_DISABLED) {
            WP_CLI::line('Status: ' . WP_CLI::colorize('%yDisabled%n'));

            return;
        }

        \Roots\bootloader();

        try {
            app(ObjectCache::class)->add('status', 'ok');
            $valid = app(ObjectCache::class)->get('status') === 'ok';
        } catch (\Exception $exception) {
            WP_CLI::line(WP_CLI::colorize('Status: %rError%n'));
            WP_CLI::error($exception);

            return;
        }

        if (!$valid) {
            WP_CLI::warning('Status: Unexpected behavior');

            return;
        }

        WP_CLI::success('Status: Connected');
    }

    /**
     * Print a value from the object cache.
     *
     * ## OPTIONS
     *
     * <key>
     * : The key of the value.
     *
     * ## EXAMPLES
     *
     *     wp object-cache get :key
     */
    public function get(array $args)
    {
        [$key] = $args;

        \Roots\bootloader();

        $result = app(ObjectCache::class)->get($key);

        WP_CLI::success($result);
    }

    /**
     * Flush the object cache, clear all data.
     *
     * ## EXAMPLES
     *
     *     wp object-cache flush
     */
    public function flush()
    {
        \Roots\bootloader();

        app(ObjectCache::class)->flush();

        WP_CLI::success('Flushed!');
    }
}
