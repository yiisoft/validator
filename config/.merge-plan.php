<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'params' => [
            'yiisoft/yii-debug' => [
                'config/params.php',
            ],
            'yiisoft/translator' => [
                'config/params.php',
            ],
            'yiisoft/aliases' => [
                'config/params.php',
            ],
            'yiisoft/profiler' => [
                'config/params.php',
            ],
            '/' => [
                'params.php',
            ],
        ],
        'di' => [
            'yiisoft/yii-debug' => [
                'config/di.php',
            ],
            'yiisoft/translator' => [
                'config/di.php',
            ],
            'yiisoft/aliases' => [
                'config/di.php',
            ],
            'yiisoft/profiler' => [
                'config/di.php',
            ],
            '/' => [
                'di.php',
            ],
        ],
        'di-console' => [
            'yiisoft/yii-debug' => [
                'config/di-console.php',
            ],
        ],
        'di-web' => [
            'yiisoft/yii-debug' => [
                'config/di-web.php',
            ],
        ],
        'di-providers' => [
            'yiisoft/yii-debug' => [
                'config/di-providers.php',
            ],
        ],
        'events-web' => [
            'yiisoft/yii-debug' => [
                'config/events-web.php',
            ],
            'yiisoft/profiler' => [
                'config/events-web.php',
            ],
        ],
        'events-console' => [
            'yiisoft/yii-debug' => [
                'config/events-console.php',
            ],
        ],
    ],
];
