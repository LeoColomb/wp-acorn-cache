<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Page Cache
    |--------------------------------------------------------------------------
    |
    |
    */

    'debug' => false,

    'default_ttl' => 0,

    'private_headers' => [
        'Authorization',
        'Cookie'
    ],

    'allow_reload' => false,

    'allow_revalidate' => false,

    'stale_while_revalidate' => 2,

    'stale_if_error' => 60,

    'trace_level' => 'none',

    'trace_header' => 'X-Acorn-Cache',

];
