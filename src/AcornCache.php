<?php

namespace LeoColomb\WPAcornCache;

use Illuminate\Support\Facades\Cache;

/**
 * Core class that implements an object cache.
 *
 * The WordPress Object Cache is used to save on trips to the database. The
 * Object Cache stores all of the cache data to memory and makes the cache
 * contents available by using a key, which is used to name and later retrieve
 * the cache contents.
 *
 * The Object Cache can be replaced by other caching mechanisms by placing files
 * in the wp-content folder which is looked at in wp-settings. If that file
 * exists, then this file will not be included.
 *
 * @since 2.0.0
 */
class AcornCache
{

    /**
     * Holds the cached objects.
     *
     * @since 2.0.0
     * @var   array
     */
    private array $cache = [];

    /**
     * Config
     *
     * @var array
     */
    protected $config = [
        'groups' => [
            'global' => [
                'blog-details',
                'blog-id-cache',
                'blog-lookup',
                'global-posts',
                'networks',
                'rss',
                'sites',
                'site-details',
                'site-lookup',
                'site-options',
                'site-transient',
                'users',
                'useremail',
                'userlogins',
                'usermeta',
                'user_meta',
                'userslugs',
            ],
            'ignored' => ['counts', 'plugins']
        ]
    ];

    /**
     * The blog prefix to prepend to keys in non-global groups.
     *
     * @since 3.5.0
     * @var   string
     */
    private string $blog_prefix;

    /**
     * The blog prefix to prepend to keys in global groups.
     *
     * @since 3.5.0
     * @var   int
     */
    private int $global_prefix;

    /**
     * Holds the value of is_multisite().
     *
     * @since 3.5.0
     * @var   bool
     */
    private bool $multisite;

    /**
     * Initialize
     *
     * @param  array $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        global $table_prefix;

        $this->config = collect($this->config)->merge($config);
        $this->cache = collect();

        // Assign global and blog prefixes for use with keys
        if (function_exists('is_multisite')) {
            $this->multisite = is_multisite();
        }

        $this->global_prefix = ($this->multisite || defined('CUSTOM_USER_TABLE') && defined('CUSTOM_USER_META_TABLE')) ? '' : $table_prefix;
        $this->blog_prefix   = $this->multisite ? get_current_blog_id() : $table_prefix;
    }

    /**
     * Adds data to the cache if it doesn't already exist.
     *
     * @since 2.0.0
     *
     * @param int|string $key    What to call the contents in the cache.
     * @param mixed      $data   The contents to store in the cache.
     * @param string     $group  Optional. Where to group the cache contents. Default 'default'.
     * @param int        $expire Optional. When to expire the cache contents. Default 0 (no expiration).
     * @return bool True on success, false if cache key and group already exist.
     */
    public function add(string $key, $data, string $group = 'default', int $expire = 0): bool
    {
        $suspend = function_exists('wp_suspend_cache_addition')
            ? wp_suspend_cache_addition()
            : false;
        if ($suspend) {
            return false;
        }

        return Cache::add($this->buildKey($key, $group), $data, $expire);
    }

    /**
     * Replaces the contents in the cache, if contents already exist.
     *
     * @since 2.0.0
     *
     * @param int|string $key    What to call the contents in the cache.
     * @param mixed      $data   The contents to store in the cache.
     * @param string     $group  Optional. Where to group the cache contents. Default 'default'.
     * @param int        $expire Optional. When to expire the cache contents. Default 0 (no expiration).
     * @return bool False if not exists, true if contents were replaced.
     */
    public function replace(string $key, $data, string $group = 'default', int $expire = 0): bool
    {
        if (! Cache::has($this->buildKey($key, $group))) {
            return false;
        }

        return Cache::put($this->buildKey($key, $group), $data, $expire);
    }

    /**
     * Removes the contents of the cache key in the group.
     *
     * If the cache key does not exist in the group, then nothing will happen.
     *
     * @since 2.0.0
     *
     * @param int|string $key        What the contents in the cache are called.
     * @param string     $group      Optional. Where the cache contents are grouped. Default 'default'.
     * @return bool False if the contents weren't deleted and true on success.
     */
    public function delete(string $key, string $group = 'default'): bool
    {
        return Cache::forget($this->buildKey($key, $group));
    }

    /**
     * Clears the object cache of all data.
     *
     * @since 2.0.0
     *
     * @return true Always returns true.
     */
    public function flush()
    {
        return Cache::flush();
    }

    /**
     * Retrieves the cache contents, if it exists.
     *
     * The contents will be first attempted to be retrieved by searching by the
     * key in the cache group. If the cache is hit (success) then the contents
     * are returned.
     *
     * On failure, the number of cache misses will be incremented.
     *
     * @since 2.0.0
     *
     * @param int|string $key   The key under which the cache contents are stored.
     * @param string     $group Optional. Where the cache contents are grouped. Default 'default'.
     * @param bool       $force Optional. Unused. Whether to force an update of the local cache
     *                          from the persistent cache. Default false.
     * @param bool       $found Optional. Whether the key was found in the cache (passed by reference).
     *                          Disambiguates a return of false, a storable value. Default null.
     * @return mixed|false The cache contents on success, false on failure to retrieve contents.
     */
    public function get(string $key, string $group = 'default', bool $force = false, bool &$found = null)
    {
        $found = true;

        return Cache::get($this->buildKey($key, $group), function () use (&$found) {
            $found = false;
        });
    }

    /**
     * Retrieves multiple values from the cache in one call.
     *
     * @since 5.5.0
     *
     * @param array  $keys  Array of keys under which the cache contents are stored.
     * @param string $group Optional. Where the cache contents are grouped. Default 'default'.
     * @param bool   $force Optional. Whether to force an update of the local cache
     *                      from the persistent cache. Default false.
     * @return array Array of values organized into groups.
     */
    public function getMultiple(array $keys, string $group = 'default', bool $force = false): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $group);
        }

        return $values;
    }

    /**
     * Sets the data contents into the cache.
     *
     * The cache contents are grouped by the $group parameter followed by the
     * $key. This allows for duplicate IDs in unique groups. Therefore, naming of
     * the group should be used with care and should follow normal function
     * naming guidelines outside of core WordPress usage.
     *
     * The $expire parameter is not used, because the cache will automatically
     * expire for each time a page is accessed and PHP finishes. The method is
     * more for cache plugins which use files.
     *
     * @since 2.0.0
     *
     * @param int|string $key    What to call the contents in the cache.
     * @param mixed      $data   The contents to store in the cache.
     * @param string     $group  Optional. Where to group the cache contents. Default 'default'.
     * @param int        $expire Not Used.
     * @return true Always returns true.
     */
    public function set($key, $data, $group = 'default', $expire = null): bool
    {
        if (in_array($group, $this->config->get('groups.ignored'))) {
            return true;
        }

        return Cache::put($this->buildKey($key, $group), $data, $expire);
    }

    /**
     * Increments numeric cache item's value.
     *
     * @since 3.3.0
     *
     * @param int|string $key    The cache key to increment
     * @param int        $offset Optional. The amount by which to increment the item's value. Default 1.
     * @param string     $group  Optional. The group the key is in. Default 'default'.
     * @return int|false The item's new value on success, false on failure.
     */
    public function incr(string $key, int $offset = 1, string $group = 'default')
    {
        if (in_array($group, $this->config->get('groups.ignored'))) {
            return false;
        }

        Cache::increment($this->buildKey($key, $group), $offset);

        return Cache::get($this->buildKey($key, $group));
    }

    /**
     * Decrements numeric cache item's value.
     *
     * @since 3.3.0
     *
     * @param int|string $key    The cache key to decrement.
     * @param int        $offset Optional. The amount by which to decrement the item's value. Default 1.
     * @param string     $group  Optional. The group the key is in. Default 'default'.
     * @return int|false The item's new value on success, false on failure.
     */
    public function decr(string $key, int $offset = 1, string $group = 'default')
    {
        if (in_array($group, $this->config->get('groups.ignored'))) {
            return false;
        }

        Cache::decrement($this->buildKey($key, $group), $offset);

        return Cache::get($this->buildKey($key, $group));
    }

    /**
     * Retrieve a value from the object cache. If it doesn't exist, run the $callback to generate and
     * cache the value.
     *
     * @param string $key The cache key.
     * @param callable $callback The callback used to generate and cache the value.
     * @param string $group Optional. The cache group. Default is empty.
     * @param int|null $expire Optional. The number of seconds before the cache entry should expire.
     *                           Default is 0 (as long as possible).
     * @return mixed The value returned from $callback, pulled from the cache when available.
     */
    public function remember(string $key, callable $callback, string $group = '', int $expire = null)
    {
        return Cache::remember($this->buildKey($key, $group), $expire, $callback);
    }

    /**
     * Retrieve and subsequently delete a value from the object cache.
     *
     * @param string $key The cache key.
     * @param string $group Optional. The cache group. Default is empty.
     * @param mixed $default Optional. The default value to return if the given key doesn't
     *                        exist in the object cache. Default is null.
     * @return mixed The cached value, when available, or $default.
     */
    public function forget(string $key, string $group = '', $default = null)
    {
        return Cache::pull($this->buildKey($key, $group), $default);
    }

    public function switchToBlog(int $blogId): void
    {
        //
    }

    public function addGlobalGroups($groups): void
    {
        //
    }

    public function addNonPersistentGroups($groups): void
    {
        //
    }

    /**
     * Builds a key for the cached object using the prefix, group and key.
     *
     * @param string $key The key under which to store the value.
     * @param string $group The group value appended to the $key.
     * @return string
     */
    protected function buildKey(string $key, string $group = 'default'): string
    {
        if (empty($group)) {
            $group = 'default';
        }

        $prefix = in_array($group, $this->config->get('groups.global')) ? $this->global_prefix : $this->blog_prefix;

        return "{$prefix}:{$group}:{$key}";
    }
}
