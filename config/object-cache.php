<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global cache groups
    |--------------------------------------------------------------------------
    |
    | When using multi-sites, data belonging to these groups are stored globally
    | without child-site granularity.
    |
    */

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

    /*
    |--------------------------------------------------------------------------
    | Non-persistent cache groups
    |--------------------------------------------------------------------------
    |
    | Data belonging to these non-persistent groups are not stored, i.e. non persistent.
    |
    */

    'non-persistent' => [
        'counts',
        'plugins',
        'themes',
    ],

];
