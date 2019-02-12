<?php

return [
    'default' => env('ARTICLE_LIST_DRIVER', 'redis'),
    'drivers' => [
        'database' => [
            'driver'     => 'mysql',
            'connection' => env('ARTICLE_DATABASE_CONNECTION', 'default'),
        ],
        'redis'    => [
            'driver'     => 'redis',
            'prefix'     => env('APP_ID', 'please_define_prefix'),
            'connection' => env('ARTICLE_REDIS_CONNECTION', 'default'),
            'store'      => [
                'summary' => env('ARTICLE_REDIS_STORE_SUMMARY', 'article_summary'),
                'author'  => env('ARTICLE_REDIS_STORE_AUTHOR', 'article_author'),
            ],
        ]
    ]
];
