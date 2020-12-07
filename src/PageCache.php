<?php

namespace LeoColomb\WPAcornCache;

class PageCache
{
    const CACHE_STATUS_HEADER_NAME = 'X-Cache-Status';
    const CACHE_STATUS_HIT = 'HIT';
    const CACHE_STATUS_MISS = 'MISS';
    const CACHE_STATUS_BYPASS = 'BYPASS';
    const CACHE_STATUS_DOWN = 'DOWN';
    const CACHE_STATUS_IGNORED = 'IGNORED';
    const CACHE_STATUS_EXPIRED = 'EXPIRED';

    /**
     * Array of functions for `create_function()`.
     *
     * @var array
     */
    protected $vary = [];

    /**
     * Set to `true` to disable the output buffer.
     *
     * @var boolean
     */
    protected $cancel = false;

    protected $keys = [];
    protected $url_key;
    protected $url_version;
    protected $key;
    protected $req_key;
    protected $status_header;
    protected $status_code;

    /**
     * Config
     *
     * @var array
     */
    protected $config = [
        'times' => 2,
        'seconds' => 120,
        'max_age' => 300,
        'group' => 'page-cache',
        'unique' => [],
        'headers' => [],
        'uncached_headers' => [
            'transfer-encoding'
        ],
        'cache_control' => true,
        'use_stale' => true,
        'noskip_cookies' => [
            'wordpress_test_cookie'
        ]
    ];

    /**
     * Initialize
     *
     * @param  array $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = collect($this->config)->merge($config);
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function setupRequest()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->keys['host'] = $_SERVER['HTTP_HOST'];
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->keys['method'] = $_SERVER['REQUEST_METHOD'];
        }

        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $query_string);
            $this->keys['query'] = $query_string;
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            if (($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
                $this->keys['path'] = substr($_SERVER['REQUEST_URI'], 0, $pos);
            } else {
                $this->keys['path'] = $_SERVER['REQUEST_URI'];
            }
        }

        $this->keys['ssl'] = is_ssl();

        $this->keys['extra'] = $this->unique;

        $this->url_key = md5(sprintf(
            '%s://%s%s',
            $this->keys['ssl'] ? 'http' : 'https',
            $this->keys['host'],
            $this->keys['path']
        ));

        $this->url_version = (int) app(Container::class)->get("{$this->url_key}_version", $this->group);
    }

    protected function addVariant($function)
    {
        $this->vary[md5($function)] = $function;
    }

    /**
     * This function is called without arguments early in the page load,
     * then with arguments during the output buffer handler.
     *
     * @param  array|false  $dimensions
     */
    public function doVariants($dimensions = false)
    {
        if ($dimensions === false) {
            $dimensions = app(Container::class)->get("{$this->url_key}_vary", $this->group);
        } else {
            app(Container::class)->set("{$this->url_key}_vary", $dimensions, $this->group, $this->max_age + 10);
        }

        if (is_array($dimensions)) {
            ksort($dimensions);

            foreach ($dimensions as $key => $function) {
                $value = $function();
                $this->keys[$key] = $value;
            }
        }
    }

    public function generateKeys()
    {
        $this->key = md5(serialize($this->keys));
        $this->req_key = "{$this->key}_reqs";
    }

    protected function statusHeader($status_header, $status_code)
    {
        $this->status_header = $status_header;
        $this->status_code = $status_code;

        return $status_header;
    }

    public function cacheStatusHeader($cache_status)
    {
        header(self::CACHE_STATUS_HEADER_NAME . ": $cache_status");
    }

    /**
     * Merge the arrays of headers into one and send them.
     *
     * @param  array  $headers1
     * @param  array  $headers2
     */
    public function doHeaders($headers1, $headers2 = [])
    {
        $headers = [];
        $keys = array_unique(array_merge(array_keys($headers1), array_keys($headers2)));

        foreach ($keys as $k) {
            $headers[$k] = [];

            if (isset($headers1[$k]) && isset($headers2[$k])) {
                $headers[$k] = array_merge((array) $headers2[$k], (array) $headers1[$k]);
            } else if (isset($headers2[$k])) {
                $headers[$k] = (array) $headers2[$k];
            } else {
                $headers[$k] = (array) $headers1[$k];
            }

            $headers[$k] = array_unique($headers[$k]);
        }

        // These headers take precedence over any previously sent with the same names
        foreach ($headers as $k => $values) {
            $clobber = true;

            foreach ($values as $v) {
                header("$k: $v", $clobber);
                $clobber = false;
            }
        }
    }

    protected function outputCallback($output)
    {
        $output = trim($output);

        if ($this->cancel !== false) {
            app(Container::class)->delete("{$this->url_key}_genlock", $this->group);
            header('X-Cache-Status: BYPASS', true);

            return $output;
        }

        // Do not cache 5xx responses
        if (isset($this->status_code) && intval($this->status_code / 100) === 5) {
            app(Container::class)->delete("{$this->url_key}_genlock", $this->group);
            header('X-Cache-Status: BYPASS', true);

            return $output;
        }

        $this->doVariants($this->vary);
        $this->generateKeys();

        $cache = [
            'version' => $this->url_version,
            'time' => isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time(),
            'status_header' => $this->status_header,
            'headers' => [],
            'output' => $output,
        ];

        foreach (headers_list() as $header) {
            [$k, $v] = array_map('trim', explode(':', $header, 2));
            $cache['headers'][$k][] = $v;
        }

        if (! empty($cache['headers']) && ! empty($this->uncached_headers)) {
            foreach ($this->uncached_headers as $header) {
                unset($cache['headers'][$header]);
            }
        }

        foreach ($cache['headers'] as $header => $values) {
            // Don't cache if cookies were set
            if (strtolower($header) === 'set-cookie') {
                app(Container::class)->delete("{$this->url_key}_genlock", $this->group);
                header('X-Cache-Status: BYPASS', true);

                return $output;
            }

            foreach ((array) $values as $value) {
                if (preg_match('/^Cache-Control:.*max-?age=(\d+)/i', "{$header}: {$value}", $matches)) {
                    $this->max_age = intval($matches[1]);
                }
            }
        }

        $cache['max_age'] = $this->max_age;

        app(Container::class)->set($this->key, $cache, $this->group, $this->max_age + $this->seconds + 30);

        app(Container::class)->delete("{$this->url_key}_genlock", $this->group);

        if ($this->cache_control) {
            // Don't clobber `Last-Modified` header if already set
            if (! isset($cache['headers']['Last-Modified'])) {
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cache['time']) . ' GMT', true);
            }

            if (! isset($cache['headers']['Cache-Control'])) {
                header("Cache-Control: max-age={$this->max_age}, must-revalidate", false);
            }
        }

        $this->doHeaders($this->headers);

        return $cache['output'];
    }

    public function handle()
    {
        $this->cacheStatusHeader($this::CACHE_STATUS_MISS);

        // Don't cache interactive scripts or API endpoints
        if (
            in_array(basename($_SERVER['SCRIPT_FILENAME']), [
            'wp-cron.php',
            'xmlrpc.php',
            ])
        ) {
            $this->cacheStatusHeader($this::CACHE_STATUS_BYPASS);

            return;
        }

        // Don't cache javascript generators
        if (strpos($_SERVER['SCRIPT_FILENAME'], 'wp-includes/js') !== false) {
            $this->cacheStatusHeader($this::CACHE_STATUS_BYPASS);

            return;
        }

        // Only cache HEAD and GET requests
        if (isset($_SERVER['REQUEST_METHOD']) && ! in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'])) {
            $this->cacheStatusHeader($this::CACHE_STATUS_BYPASS);

            return;
        }

        // Don't cache when cookies indicate a cache-exempt visitor
        if (is_array($_COOKIE) && ! empty($_COOKIE)) {
            foreach (array_keys($_COOKIE) as $cookie) {
                if (in_array($cookie, $this->noskip_cookies)) {
                    continue;
                }

                if (
                    strpos($cookie, 'wp') === 0 ||
                    strpos($cookie, 'wordpress') === 0 ||
                    strpos($cookie, 'comment_author') === 0
                ) {
                    $this->cacheStatusHeader($this::CACHE_STATUS_BYPASS);

                    return;
                }
            }
        }

        if (! defined('WP_CONTENT_DIR')) {
            $this->cacheStatusHeader($this::CACHE_STATUS_DOWN);

            return;
        }

        // Cache is disabled
        if ($this->max_age < 1) {
            $this->cacheStatusHeader($this::CACHE_STATUS_BYPASS);

            return;
        }

        // Necessary to prevent clients using cached version after login cookies set
        if (defined('WP_VARY_COOKIE') && WP_VARY_COOKIE) {
            header('Vary: Cookie', false);
        }

        app(Container::class)->addGlobalGroups([$this->group]);

        $this->setupRequest();
        $this->doVariants();
        $this->generateKeys();

        $genlock = false;
        $do_cache = false;
        $serve_cache = false;
        $cachedValue = app(Container::class)->get($this->key, $this->group);

        if (isset($cachedValue['version']) && $cachedValue['version'] !== $this->url_version) {
            // Refresh the cache if a newer version is available
            $this->cacheStatusHeader($this::CACHE_STATUS_EXPIRED);
            $do_cache = true;
        } else if ($this->seconds < 1 || $this->times < 2) {
            if (is_array($cachedValue) && time() < $cachedValue['time'] + $cachedValue['max_age']) {
                $do_cache = false;
                $serve_cache = true;
            } else if (is_array($cachedValue) && $this->use_stale) {
                $do_cache = true;
                $serve_cache = true;
            } else {
                $do_cache = true;
            }
        } else if (! is_array($cachedValue) || time() >= $cachedValue['time'] + $this->max_age - $this->seconds) {
            // No cache item found, or ready to sample traffic again at the end of the cache life

            app(Container::class)->add($this->req_key, 0, $this->group);
            $requests = app(Container::class)->incr($this->req_key, 1, $this->group);

            if ($requests >= $this->times) {
                if (is_array($cachedValue) && time() >= $cachedValue['time'] + $cachedValue['max_age']) {
                    $this->cacheStatusHeader($this::CACHE_STATUS_EXPIRED);
                }

                app(Container::class)->delete($this->req_key, $this->group);
                $do_cache = true;
            } else {
                $this->cacheStatusHeader($this::CACHE_STATUS_IGNORED);
                $do_cache = false;
            }
        }

        // Obtain cache generation lock
        if ($do_cache) {
            $genlock = app(Container::class)->add("{$this->url_key}_genlock", 1, $this->group, 10);
        }

        if (
            $serve_cache &&
            isset($cachedValue['time'], $cachedValue['max_age']) &&
            time() < $cachedValue['time'] + $cachedValue['max_age']
        ) {
            // Respect ETags
            $three04 = false;

            if (
                isset($_SERVER['HTTP_IF_NONE_MATCH'], $cachedValue['headers']['ETag'][0]) &&
                $_SERVER['HTTP_IF_NONE_MATCH'] == $cachedValue['headers']['ETag'][0]
            ) {
                $three04 = true;
            } else if ($this->cache_control && isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $client_time = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);

                if (isset($cachedValue['headers']['Last-Modified'][0])) {
                    $cache_time = strtotime($cachedValue['headers']['Last-Modified'][0]);
                } else {
                    $cache_time = $cachedValue['time'];
                }

                if ($client_time >= $cache_time) {
                    $three04 = true;
                }
            }

            // Use the cache save time for `Last-Modified` so we can issue "304 Not Modified",
            // but don't clobber a cached `Last-Modified` header.
            if ($this->cache_control && ! isset($cachedValue['headers']['Last-Modified'][0])) {
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cachedValue['time']) . ' GMT', true);
                header('Cache-Control: max-age=' . ($cachedValue['max_age'] - time() + $cachedValue['time']) . ', must-revalidate', true);
            }

            $this->doHeaders($this->headers, $cachedValue['headers']);

            if ($three04) {
                $protocol = $_SERVER['SERVER_PROTOCOL'];

                if (! preg_match('/^HTTP\/[0-9]{1}.[0-9]{1}$/', $protocol)) {
                    $protocol = 'HTTP/1.0';
                }

                header("{$protocol} 304 Not Modified", true, 304);
                $this->cacheStatusHeader($this::CACHE_STATUS_HIT);
                exit;
            }

            if (! empty($cachedValue['status_header'])) {
                header($cachedValue['status_header'], true);
            }

            $this->cacheStatusHeader($this::CACHE_STATUS_HIT);

            if ($do_cache && function_exists('fastcgi_finish_request')) {
                echo $cachedValue['output'];
                fastcgi_finish_request();
            } else {
                echo $cachedValue['output'];
                exit;
            }
        }

        if (! $do_cache || ! $genlock) {
            return;
        }

        $wp_filter['status_header'][10]['page_cache'] = [
            'function' => [&$this, 'status_header'],
            'accepted_args' => 2
        ];

        ob_start([&$this, 'outputCallback']);
    }
}
