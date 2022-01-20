<?php

declare(strict_types=1);

use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/** @var array $params */

return [
    ValidatorInterface::class => Validator::class,
    FormatterInterface::class => [
        '__class' => Formatter::class,
        'locale' => $params['yiisoft/translator']['locale'],
    ],
];
