<?php

/**
 * Object Cache API
 *
 * @link https://developer.wordpress.org/reference/classes/wp_object_cache/
 */

if (! class_exists('\\LeoColomb\\WPAcornCache\\AcornCache')) {
    return;
}

use LeoColomb\WPAcornCache\AcornCache;

use function Roots\app;

/**
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 * @see    AcornCache::add()
 *
 * @param  int|string $key    The cache key to use for retrieval later.
 * @param  mixed      $data   The data to add to the cache.
 * @param  string     $group  Optional. The group to add the cache to. Enables the same key
 *                            to be used across groups. Default empty.
 * @param  int        $expire Optional. When the cache data should expire, in seconds.
 *                            Default 0 (no expiration).
 * @return bool True on success, false if cache key and group already exist.
 */
function wp_cache_add($key, $data, $group = '', $expire = 0)
{
    return app(AcornCache::class)->add($key, $data, $group, $expire);
}

/**
 * Closes the cache.
 *
 * This function has ceased to do anything since WordPress 2.5. The
 * functionality was removed along with the rest of the persistent cache.
 *
 * This does not mean that plugins can't implement this function when they need
 * to make sure that the cache is cleaned up after WordPress no longer needs it.
 *
 * @return true Always returns true.
 */
function wp_cache_close()
{
    return true;
}

/**
 * Decrements numeric cache item's value.
 *
 * @see    AcornCache::decr()
 *
 * @param  int|string $key    The cache key to decrement.
 * @param  int        $offset Optional. The amount by which to decrement the item's value. Default 1.
 * @param  string     $group  Optional. The group the key is in. Default empty.
 * @return int|false  The item's new value on success, false on failure.
 */
function wp_cache_decr($key, $offset = 1, $group = '')
{
    return app(AcornCache::class)->decr($key, $offset, $group);
}

/**
 * Removes the cache contents matching key and group.
 *
 * @see    AcornCache::delete()
 *
 * @param  int|string $key   What the contents in the cache are called.
 * @param  string     $group Optional. Where the cache contents are grouped. Default empty.
 * @return bool True on successful removal, false on failure.
 */
function wp_cache_delete($key, $group = '')
{
    return app(AcornCache::class)->delete($key, $group);
}

/**
 * Removes all cache items.
 *
 * @see    AcornCache::flush()
 *
 * @return bool True on success, false on failure.
 */
function wp_cache_flush()
{
    return app(AcornCache::class)->flush();
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 * @see    AcornCache::get()
 *
 * @param  int|string $key   The key under which the cache contents are stored.
 * @param  string     $group Optional. Where the cache contents are grouped. Default empty.
 * @param  bool       $force Optional. Whether to force an update of the local cache from the persistent
 *                           cache. Default false.
 * @param  bool       $found Optional. Whether the key was found in the cache (passed by reference).
 *                           Disambiguates a return of false, a storable value. Default null.
 * @return mixed|false The cache contents on success, false on failure to retrieve contents.
 */
function wp_cache_get($key, $group = '', $force = false, &$found = null)
{
    return app(AcornCache::class)->get($key, $group, $found);
}

/**
 * Retrieves multiple values from the cache in one call.
 *
 * @see AcornCache::getMultiple()
 *
 * @param array  $keys  Array of keys under which the cache contents are stored.
 * @param string $group Optional. Where the cache contents are grouped. Default empty.
 * @param bool   $force Optional. Whether to force an update of the local cache
 *                      from the persistent cache. Default false.
 * @return array Array of values organized into groups.
 */
function wp_cache_get_multiple($keys, $group = '', $force = false)
{
    return app(AcornCache::class)->getMultiple($keys, $group, $force);
}

/**
 * Increment numeric cache item's value
 *
 * @see    AcornCache::incr()
 *
 * @param  int|string $key    The key for the cache contents that should be incremented.
 * @param  int        $offset Optional. The amount by which to increment the item's value. Default 1.
 * @param  string     $group  Optional. The group the key is in. Default empty.
 * @return int|false The item's new value on success, false on failure.
 */
function wp_cache_incr($key, $offset = 1, $group = '')
{
    return app(AcornCache::class)->incr($key, $offset, $group);
}

/**
 * Sets up Object AcornCache Global and assigns it.
 *
 * @global AcornCache $wp_object_cache
 *
 * @throws Exception
 */
function wp_cache_init()
{
    $GLOBALS['wp_object_cache'] = app(AcornCache::class);
}

/**
 * Replaces the contents of the cache with new data.
 *
 * @see    AcornCache::replace()
 *
 * @param  int|string $key    The key for the cache data that should be replaced.
 * @param  mixed      $data   The new data to store in the cache.
 * @param  string     $group  Optional. The group for the cache data that should be replaced.
 *                            Default empty.
 * @param  int        $expire Optional. When to expire the cache contents, in seconds.
 *                            Default 0 (no expiration).
 * @return bool False if original value does not exist, true if contents were replaced
 */
function wp_cache_replace($key, $data, $group = '', $expire = 0)
{
    return app(AcornCache::class)->replace($key, $data, $group, $expire);
}

/**
 * Saves the data to the cache.
 *
 * Differs from wp_cache_add() and wp_cache_replace() in that it will always write data.
 *
 * @see    AcornCache::set()
 *
 * @param  int|string $key    The cache key to use for retrieval later.
 * @param  mixed $data   The contents to store in the cache.
 * @param  string     $group  Optional. Where to group the cache contents. Enables the same key
 *                            to be used across groups. Default empty.
 * @param  int        $expire Optional. When to expire the cache contents, in seconds.
 *                            Default 0 (no expiration).
 * @return bool True on success, false on failure.
 */
function wp_cache_set($key, $data, $group = '', $expire = 0)
{
    return app(AcornCache::class)->set($key, $data, $group, $expire);
}

/**
 * Switches the internal blog ID.
 *
 * This changes the blog id used to create keys in blog specific groups.
 *
 * @see    AcornCache::switch_to_blog()
 *
 * @param int $blog_id Site ID.
 */
function wp_cache_switch_to_blog($blog_id)
{
    app(AcornCache::class)->switchToBlog($blog_id);
}

/**
 * Adds a group or set of groups to the list of global groups.
 *
 * @see    AcornCache::addGlobalGroups()
 *
 * @param string|array $groups A group or an array of groups to add.
 */
function wp_cache_add_global_groups($groups)
{
    app(AcornCache::class)->addGlobalGroups($groups);
}

/**
 * Adds a group or set of groups to the list of non-persistent groups.
 *
 * @see    AcornCache::addNonPersistentGroups()
 *
 * @param string|array $groups A group or an array of groups to add.
 */
function wp_cache_add_non_persistent_groups($groups)
{
    app(AcornCache::class)->addNonPersistentGroups($groups);
}

/**
 * Retrieve a value from the object cache. If it doesn't exist, run the $callback to generate and
 * cache the value.
 *
 * @param string   $key      The cache key.
 * @param callable $callback The callback used to generate and cache the value.
 * @param string   $group    Optional. The cache group. Default is empty.
 * @param int      $expire   Optional. The number of seconds before the cache entry should expire.
 *                           Default is 0 (as long as possible).
 *
 * @return mixed The value returned from $callback, pulled from the cache when available.
 */
function wp_cache_remember($key, $callback, $group = '', $expire = 0)
{
    return app(AcornCache::class)->remember($key, $callback, $group, $expire);
}

/**
 * Retrieve and subsequently delete a value from the object cache.
 *
 * @param string $key     The cache key.
 * @param string $group   Optional. The cache group. Default is empty.
 * @param mixed  $default Optional. The default value to return if the given key doesn't
 *                        exist in the object cache. Default is null.
 *
 * @return mixed The cached value, when available, or $default.
 */
function wp_cache_forget($key, $group = '', $default = null)
{
    return app(AcornCache::class)->forget($key, $group, $default);
}
