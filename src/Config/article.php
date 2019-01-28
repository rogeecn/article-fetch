<?php

return [
    'default' => env('ARTICLE_LIST_DRIVER', 'database'),
    'drivers' => [
        'database' => 'mysql',
        'redis'    => [
            'prefix'     => env('ARTICLE_REDIS_PREFIX', ''),
            'connection' => env('ARTICLE_REDIS_CONNECTION', 'default'),
            'store'      => [
                'summary' => env('ARTICLE_REDIS_STORE_SUMMARY', 'article_data'),
            ],
        ]
    ]
];
