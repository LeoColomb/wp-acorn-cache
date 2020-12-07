<?php

return [
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
        'ignored' => ['counts', 'plugins'],
    ],

    'times' => 2,
    'seconds' => 120,
    'max_age' => 300,
    'group' => 'page-cache',
    'unique' => [],
    'headers' => [],
    'uncached_headers' => [
        'transfer-encoding',
    ],
    'cache_control' => true,
    'use_stale' => true,
    'noskip_cookies' => [
        'wordpress_test_cookie',
    ],
];
