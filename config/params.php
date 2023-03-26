<?php

declare(strict_types=1);

use Yiisoft\Validator\Debug\ValidatorCollector;
use Yiisoft\Validator\Debug\ValidatorInterfaceProxy;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

return [
    'yiisoft/validator' => [
        'translation.category' => Validator::DEFAULT_TRANSLATION_CATEGORY,
    ],

    'yiisoft/yii-debug' => [
        'collectors' => [
            ValidatorCollector::class,
        ],
        'trackedServices' => [
            ValidatorInterface::class => [ValidatorInterfaceProxy::class, ValidatorCollector::class],
        ],
    ],
];
